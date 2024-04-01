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

class PlantingController {
    #[Route('get', '/plantings')]
    public function plantings(Request $request, Application $app) {
        $sort_prop = $request->GET['sort_by'] ?? 'date';
        $sort_dir = $request->GET['sort_dir'] ?? 1;
        $sort_dir = intval($sort_dir);
        if ($sort_dir < -1 || $sort_dir > 1) {
            $sort_dir = 1;
        }

        $filter = [];

        if (\array_key_exists('filter', $request->GET) && $request->GET['filter'] !== '') {
            $filter['status'] = $request->GET['filter'];
        }

        $allPlantings = $app->db->plantings->get_all(
            $filter,
            $sort_prop,
            $sort_dir,
        );

        echo $app->templates->render(
            'plantings::index',
            [
                'allPlantings' => $allPlantings,
                'sort_by' => $sort_prop,
                'sort_dir' => $sort_dir,
                'filter' => $filter['status'] ?? '',
            ],
        );
    }

    #[Route('get', '/plantings/{id}')]
    public function plantings_view_get(Request $request, Application $app, string $id) {
        $planting = $app->db->plantings->find_by_id($id);
        $logs = $app->db->logs->get_planting_logs($id, $planting->date->format('Y-m-d H:i:s'));

        echo $app->templates->render('plantings::view',
            [
                'planting' => $planting,
                'logs' => $logs,
            ]
        );
    }

    #[Filter('LoginRequired')]
    #[Route('get', '/plantings/new')]
    public function plantings_new_get(Request $request, Application $app) {
        echo $app->templates->render(
            'plantings::new',
            [
                'seeds' => $this->get_seed_select_data($app),
                'beds' => $this->get_bed_select_data($app),
                'auto_bed' => $request->GET['bed'] ?? '',
                'auto_row' => $request->GET['row'] ?? '1',
                'auto_col' => $request->GET['col'] ?? '1',
            ],
        );
    }

    private function get_seed_select_data(Application $app): array {
        $seeds = $app->db->seeds->get_all('common_name');
        $seed_data = [];
        foreach ($seeds as $seed) {
            $seed_data []= [
                'name' => $seed->display_string(),
                'id' => $seed->get_id(),
            ];
        }
        return $seed_data;
    }

    private function get_bed_select_data(Application $app): array {
        $beds = $app->db->beds->get_all('name');
        $bed_data = [];
        foreach ($beds as $bed) {
            $bed_data []= [
                'name' => $bed->display_string(),
                'id' => $bed->get_id(),
            ];
        }
        return $bed_data;
    }

    #[Filter('LoginRequired')]
    #[Route('post', '/plantings/new')]
    public function plantings_new_post(Request $request, Application $app) {
        $form_vars = $request->POST;

        $record = new Models\Planting();

        $record->date = new \DateTimeImmutable($form_vars['planting_date'] ?? 'now');
        $record->row = \intval($form_vars['row']);
        $record->column = \intval($form_vars['column']);
        $bed = $app->db->beds->find_by_id(new ObjectId($form_vars['bed']));
        $record->bed = $bed;
        $seed = $app->db->seeds->find_by_id(new ObjectId($form_vars['seed']));
        $record->seed = $seed;
        $record->status = 'Active';
        $record->is_transplant = $form_vars['is_transplant'] === 'Yes';
        $record->notes = $form_vars['notes'];
        $record->tray_id = $form_vars['tray_id'];

        $app->db->plantings->create($record);

        $app->templates->addData([
            'toast' => "Created new planting (<a href=\"/plantings/{$record->get_id()}\">{$seed->display_string()}</a>)"
        ]);

        echo $app->templates->render(
            'plantings::new',
            [
                'seeds' => $this->get_seed_select_data($app),
                'beds' => $this->get_bed_select_data($app),
                'auto_bed' => $request->GET['bed'] ?? '',
            ],
        );
    }

    #[Filter('LoginRequired')]
    #[Route('post', '/plantings')]
    public function plantings_post(Request $request, Application $app) {
        switch ($request->POST['action']) {
            case 'delete_planting':
                $this->plantings_delete($request, $app);
                break;
        }

        $this->plantings($request, $app);
    }

    private function plantings_delete(Request $request, Application $app) {
        $planting = $app->db->plantings->find_by_id($request->POST['planting_id']);

        if (\is_null($planting)) {
            $toast_msg = "Planting does not exist with ID {$request->POST['planting_id']}";
        } else {
            try {
                $app->db->plantings->delete($planting);
            } catch (\Exception $e) {
                $toast_msg = 'Error deleting seed: '.$e;
            }

            $toast_msg = "Planting deleted";
        }

        $app->templates->addData(['toast' => $toast_msg]);
    }

    #[Filter('LoginRequired')]
    #[Route('get', '/plantings/edit/{id}')]
    public function plantings_edit_get(Request $request, Application $app, string $id) {
        $planting = $app->db->plantings->find_by_id($id);

        echo $app->templates->render('plantings::edit',
            [
                'planting' => $planting,
                'seeds' => $this->get_seed_select_data($app),
                'beds' => $this->get_bed_select_data($app),
            ]
        );
    }

    #[Filter('LoginRequired')]
    #[Route('post', '/plantings/edit/{id}')]
    public function plantings_edit_post(Request $request, Application $app, string $id) {
        $form_vars = $request->POST;
        $record = $app->db->plantings->find_by_id($id);

        $record->date = new \DateTimeImmutable($form_vars['planting_date'] ?? 'now');
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

        if ($record->status === 'Harvested') {
            $record->harvest_date = new \DateTimeImmutable();
        } else {
            $record->harvest_date = null;
        }

        $app->db->plantings->save($record);

        $app->templates->addData([
            'toast' => "Saved planting (<a href=\"/plantings/{$record->get_id()}\">{$seed->display_string()}</a>)"
        ]);

        echo $app->templates->render('plantings::edit',
            [
                'planting' => $record,
                'seeds' => $this->get_seed_select_data($app),
                'beds' => $this->get_bed_select_data($app),
            ]
        );
    }

    #[Route('get', '/plantings/gallery/{id}')]
    public function plantings_gallery_get(Request $request, Application $app, string $id) {
        $dir = $request->GET['dir'] ?? 'desc';
        $planting = $app->db->plantings->find_by_id($id);

        $log_dir = $dir === 'asc' ? 1 : -1;
        $logs = $app->db->logs->get_planting_logs($id, $planting->date->format('Y-m-d H:i:s'), 'date', $log_dir);

        echo $app->templates->render('plantings::gallery',
            [
                'planting' => $planting,
                'logs' => $logs,
                'sort_dir' => $dir === 'desc' ? 'asc' : 'desc',
            ]
        );
    }
}
