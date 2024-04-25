<?php

declare(strict_types=1);

namespace Garden\Controllers;

use Garden\Models;
use Garden\Application;
use Garden\Lib\LoginRequired;
use Onesimus\Router\Http\Request;
use Onesimus\Router\Attr\Route;
use Onesimus\Router\Attr\Filter;
use MongoDB\BSON\ObjectId;

class PlantingController
{
    #[Route('get', '/plantings')]
    public function plantings(Request $request, Application $app)
    {
        $tag_filter = $request->GET['tag'] ?? '';
        $sort_prop = $request->GET['sort_by'] ?? 'date';
        $sort_dir = $request->GET['sort_dir'] ?? -1;
        $sort_dir = intval($sort_dir);
        if ($sort_dir < -1 || $sort_dir > 1) {
            $sort_dir = 1;
        }
        $status_filter = $request->GET['filter'] ?? 'Active';
        if ($status_filter === '') {
            $status_filter = 'Active';
        }

        $filter = [];

        if ($status_filter !== 'All') {
            $filter['status'] = $status_filter;
        }
        if ($tag_filter !== '') {
            $filter['custom_tags'] = ['$in' => [$tag_filter]];
        }

        $allPlantings = $app->db->plantings->get_all(
            $sort_prop,
            $sort_dir,
            $filter,
        );

        echo $app->templates->render(
            'plantings::index',
            [
                'allPlantings' => $allPlantings,
                'allTags' => $app->db->plantings->get_all_tags(),
                'sort_by' => $sort_prop,
                'sort_dir' => $sort_dir,
                'filter' => $status_filter,
                'no_tag_link' => http_build_query([
                    'filter' => $status_filter,
                ]),
            ],
        );
    }

    #[Route('get', '/plantings/{id}')]
    public function plantings_view_get(Request $request, Application $app, string $id)
    {
        $planting = $app->db->plantings->find_by_id($id);
        $logs = $app->db->logs->get_planting_logs($planting);

        echo $app->templates->render(
            'plantings::view',
            [
                'planting' => $planting,
                'logs' => $logs,
            ]
        );
    }

    #[Filter('LoginRequired')]
    #[Route('get', '/plantings/new')]
    public function plantings_new_get(Request $request, Application $app)
    {
        echo $app->templates->render(
            'plantings::new',
            [
                'seeds' => $this->get_seed_select_data($app),
                'beds' => $this->get_bed_select_data($app),
                'auto_bed' => $request->GET['bed'] ?? '',
                'auto_row' => $request->GET['row'] ?? '1',
                'auto_col' => $request->GET['col'] ?? '1',
                'auto_seed' => $request->GET['seed'] ?? '',
            ],
        );
    }

    private function get_seed_select_data(Application $app): array
    {
        $seeds = $app->db->seeds->get_all('common_name');
        $seed_data = [];
        foreach ($seeds as $seed) {
            $seed_data[] = [
                'name' => $seed->display_string(),
                'id' => $seed->get_id(),
            ];
        }
        return $seed_data;
    }

    private function get_bed_select_data(Application $app): array
    {
        $beds = $app->db->beds->get_all('name');
        $bed_data = [];
        foreach ($beds as $bed) {
            $bed_data[] = [
                'name' => $bed->display_string(),
                'id' => $bed->get_id(),
            ];
        }
        return $bed_data;
    }

    #[Filter('LoginRequired')]
    #[Route('post', '/plantings/new')]
    public function plantings_new_post(Request $request, Application $app)
    {
        $form_vars = $request->POST;
        $record = new Models\Planting();

        $row = $form_vars['row'];
        $col = $form_vars['column'];

        $row_start = 0;
        $row_end = 0;
        $col_start = 0;
        $col_end = 0;

        if (str_contains($row, "-")) {
            $row_parts = explode("-", $row);
            $row_start = \intval($row_parts[0]);
            $row_end = \intval($row_parts[1]);
        } else {
            $row_start = \intval($row);
            $row_end = \intval($row);
        }

        if (str_contains($col, "-")) {
            $col_parts = explode("-", $col);
            $col_start = \intval($col_parts[0]);
            $col_end = \intval($col_parts[1]);
        } else {
            $col_start = \intval($col);
            $col_end = \intval($col);
        }

        $tags = [];

        $custom_tags = explode(',', $form_vars['tags']);
        foreach ($custom_tags as $tag) {
            array_push($tags, trim($tag));
        }

        for ($i = $row_start; $i <= $row_end; $i++) {
            for ($j = $col_start; $j <= $col_end; $j++) {
                $record->date = new \DateTimeImmutable($form_vars['planting_date'] ?? 'now');
                $record->row = $i;
                $record->column = $j;
                $bed = $app->db->beds->find_by_id(new ObjectId($form_vars['bed']));
                $record->bed = $bed;
                $seed = $app->db->seeds->find_by_id(new ObjectId($form_vars['seed']));
                $record->seed = $seed;
                $record->status = 'Active';
                $record->is_transplant = $form_vars['is_transplant'] === 'Yes';
                $record->notes = $form_vars['notes'];
                $record->tray_id = $form_vars['tray_id'];
                $record->transplant_log = new Models\ArrayOfTransplants();
                $record->tags = $tags;
                $record->count = \intval($form_vars['count']);
                $app->db->plantings->create($record);
            }
        }


        $app->templates->addData([
            'toast' => "Created new planting (<a href=\"/plantings/{$record->get_id()}\">{$seed->display_string()}</a>)"
        ]);

        echo $app->templates->render(
            'plantings::new',
            [
                'seeds' => $this->get_seed_select_data($app),
                'beds' => $this->get_bed_select_data($app),
                'auto_bed' => $request->GET['bed'] ?? '',
                'auto_seed' => $request->GET['seed'] ?? '',
            ],
        );
    }

    #[Filter('LoginRequired')]
    #[Route('post', '/plantings')]
    public function plantings_post(Request $request, Application $app)
    {
        switch ($request->POST['action']) {
            case 'delete_planting':
                $this->plantings_delete($request, $app);
                break;
            case 'bulk_delete':
                $this->plantings_bulk_delete($request, $app);
                break;
        }

        $this->plantings($request, $app);
    }

    private function plantings_delete(Request $request, Application $app)
    {
        $planting = $app->db->plantings->find_by_id($request->POST['planting_id']);

        if (\is_null($planting)) {
            $toast_msg = "Planting does not exist with ID {$request->POST['planting_id']}";
        } else {
            try {
                $app->db->plantings->delete($planting);
            } catch (\Exception $e) {
                $toast_msg = 'Error deleting seed: ' . $e;
            }

            $toast_msg = "Planting deleted";
        }

        $app->templates->addData(['toast' => $toast_msg]);
    }

    private function plantings_bulk_delete(Request $request, Application $app)
    {
        $selections = explode(",", $request->POST['selection'] ?? '');

        foreach ($selections as $selection) {
            $planting = $app->db->plantings->find_by_id($selection);

            if (!\is_null($planting)) {
                try {
                    $app->db->plantings->delete($planting);
                } catch (\Exception $e) {
                }
            }
        }
        $toast_msg = "Plantings deleted";

        $app->templates->addData(['toast' => $toast_msg]);
    }

    #[Filter('LoginRequired')]
    #[Route('get', '/plantings/edit')]
    public function plantings_bulk_edit_get(Request $request, Application $app)
    {
        $selected_param = trim($request->GET['selected']);

        if ($selected_param === '') {
            header('Location: /plantings?filter=Active', true, 307);
            return;
        }

        $selected = explode(",", $selected_param);

        $plantings = [];
        foreach ($selected as $selection) {
            $plantings[] = $app->db->plantings->find_by_id($selection);
        }

        echo $app->templates->render(
            'plantings::bulk_edit',
            [
                'plantings' => $plantings,
                'seeds' => $this->get_seed_select_data($app),
                'beds' => $this->get_bed_select_data($app),
            ]
        );
    }

    #[Filter('LoginRequired')]
    #[Route('post', '/plantings/edit')]
    public function plantings_bulk_edit_post(Request $request, Application $app)
    {
        $selected = explode(",", $request->GET['selected']);
        $new_status = $request->POST['status'];
        $change_status = $new_status !== 'Change:';

        $change_tags = $request->POST['tags'] !== '';
        $tags = [];
        $custom_tags = explode(',', $request->POST['tags']);
        foreach ($custom_tags as $tag) {
            array_push($tags, trim($tag));
        }

        foreach ($selected as $selection) {
            $planting = $app->db->plantings->find_by_id($selection);
            if ($change_status) {
                $planting->status = $new_status;
            }
            if ($change_tags) {
                $planting->tags = $tags;
            }
            $app->db->plantings->save($planting);
        }

        $app->templates->addData(['toast' => "Plantings updated"]);
        header("Location: /plantings?filter=Active", true, 302);
    }

    #[Filter('LoginRequired')]
    #[Route('get', '/plantings/edit/{id}')]
    public function plantings_edit_get(Request $request, Application $app, string $id)
    {
        $planting = $app->db->plantings->find_by_id($id);

        echo $app->templates->render(
            'plantings::edit',
            [
                'planting' => $planting,
                'seeds' => $this->get_seed_select_data($app),
                'beds' => $this->get_bed_select_data($app),
            ]
        );
    }

    #[Filter('LoginRequired')]
    #[Route('post', '/plantings/edit/{id}')]
    public function plantings_edit_post(Request $request, Application $app, string $id)
    {
        $form_vars = $request->POST;
        $record = $app->db->plantings->find_by_id($id);

        $record->date = new \DateTimeImmutable($form_vars['planting_date'] ?? 'now');
        if ($form_vars['sprouting_date']) {
            $record->sprout_date = new \DateTimeImmutable($form_vars['sprouting_date']);
        }
        $record->row = \intval($form_vars['row']);
        $record->column = \intval($form_vars['column']);
        $bed = $app->db->beds->find_by_id(new ObjectId($form_vars['bed']));
        $record->bed = $bed;
        $seed = $app->db->seeds->find_by_id(new ObjectId($form_vars['seed']));
        $record->seed = $seed;
        $record->status = $form_vars['status'] ?? 'Active';
        $record->is_transplant = $form_vars['is_transplant'] === 'Yes';
        $record->notes = $form_vars['notes'];
        $record->tray_id = $form_vars['tray_id'];
        $record->tags = [];
        $record->count = \intval($form_vars['count']);

        $custom_tags = explode(',', $form_vars['tags']);
        foreach ($custom_tags as $tag) {
            array_push($record->tags, trim($tag));
        }

        if ($record->status === 'Harvested') {
            $record->harvest_date = new \DateTimeImmutable();
        } else {
            $record->harvest_date = null;
        }

        $app->db->plantings->save($record);

        header("Location: /plantings/{$id}", true, 302);
    }

    #[Route('get', '/plantings/gallery/{id}')]
    public function plantings_gallery_get(Request $request, Application $app, string $id)
    {
        $dir = $request->GET['dir'] ?? 'desc';
        $planting = $app->db->plantings->find_by_id($id);

        $log_dir = $dir === 'asc' ? 1 : -1;
        $logs = $app->db->logs->get_planting_logs($planting, 'date', $log_dir);

        echo $app->templates->render(
            'plantings::gallery',
            [
                'planting' => $planting,
                'logs' => $logs,
                'sort_dir' => $dir === 'desc' ? 'asc' : 'desc',
            ]
        );
    }

    #[Filter('LoginRequired')]
    #[Route('get', '/plantings/transplant/{id}')]
    public function plantings_transplant_get(Request $request, Application $app, string $id)
    {
        $planting = $app->db->plantings->find_by_id($id);

        echo $app->templates->render(
            'plantings::transplant',
            [
                'planting' => $planting,
                'beds' => $this->get_bed_select_data($app),
            ]
        );
    }

    #[Filter('LoginRequired')]
    #[Route('post', '/plantings/transplant/{id}')]
    public function plantings_transplant_post(Request $request, Application $app, string $id)
    {
        $form_vars = $request->POST;

        // Build and create transplant log
        $planting = $app->db->plantings->find_by_id($id);
        $record = new Models\Transplant();

        $from = new Models\PlantingLocation();
        $from->row = \intval($form_vars['old_row']);
        $from->column = \intval($form_vars['old_column']);
        $from->bed = $app->db->beds->find_by_id(new ObjectId($form_vars['old_bed']));
        $from->tray_id = $form_vars['old_tray_id'];

        $to = new Models\PlantingLocation();
        $to->row = \intval($form_vars['new_row']);
        $to->column = \intval($form_vars['new_column']);
        $to->bed = $app->db->beds->find_by_id(new ObjectId($form_vars['new_bed']));
        $to->tray_id = $form_vars['new_tray_id'];

        $record->from = $from;
        $record->to = $to;
        $record->date = new \DateTimeImmutable($form_vars['transplant_date'] ?? 'now');
        $record->notes = $form_vars['notes'];

        $app->db->transplants->create($record);

        // Update planting with transplant log and current location
        $planting->row = $to->row;
        $planting->column = $to->column;
        $planting->bed = $to->bed;
        $planting->tray_id = $to->tray_id;
        $planting->transplant_log[] = $record;
        $app->db->plantings->save($planting);

        header("Location: /plantings/{$id}", true, 302);
    }
}