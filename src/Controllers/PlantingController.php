<?php
declare(strict_types=1);
namespace Root\Garden\Controllers;

use Root\Garden\Models;
use Root\Garden\Application;
use MongoDB\BSON\ObjectId;

class PlantingController {
    private Application $app;

    public function __construct($app) {
        $this->app = $app;
    }

    public function plantings() {
        $sort_prop = $this->app->request->GET['sort_by'] ?? 'date';
        $sort_dir = $this->app->request->GET['sort_dir'] ?? 1;
        $sort_dir = intval($sort_dir);
        if ($sort_dir < -1 || $sort_dir > 1) {
            $sort_dir = 1;
        }

        $filter = [];

        if (\array_key_exists('filter', $this->app->request->GET) && $this->app->request->GET['filter'] !== '') {
            $filter['status'] = $this->app->request->GET['filter'];
        }

        $allPlantings = $this->app->db->plantings->get_all(
            $filter,
            $sort_prop,
            $sort_dir,
        );

        echo $this->app->templates->render(
            'plantings::index',
            [
                'allPlantings' => $allPlantings,
                'sort_by' => $sort_prop,
                'sort_dir' => $sort_dir,
                'filter' => $filter['status'] ?? '',
            ],
        );
    }

    public function plantings_view_get($id) {
        $planting = $this->app->db->plantings->find_by_id($id);
        $logs = $this->app->db->logs->get_planting_logs($id);

        echo $this->app->templates->render('plantings::view',
            [
                'planting' => $planting,
                'logs' => $logs,
            ]
        );
    }

    public function plantings_new_get() {
        echo $this->app->templates->render(
            'plantings::new',
            [
                'seeds' => $this->get_seed_select_data(),
                'beds' => $this->get_bed_select_data(),
            ],
        );
    }

    private function get_seed_select_data(): array {
        $seeds = $this->app->db->seeds->get_all('common_name');
        $seed_data = [];
        foreach ($seeds as $seed) {
            $seed_data []= [
                'name' => $seed->display_string(),
                'id' => $seed->get_id(),
            ];
        }
        return $seed_data;
    }

    private function get_bed_select_data(): array {
        $beds = $this->app->db->beds->get_all('name');
        $bed_data = [];
        foreach ($beds as $bed) {
            $bed_data []= [
                'name' => $bed->display_string(),
                'id' => $bed->get_id(),
            ];
        }
        return $bed_data;
    }

    public function plantings_new_post() {
        $form_vars = $this->app->request->POST;

        $record = new Models\Planting($this->app->db, null);

        $record->date = new \DateTimeImmutable();
        $record->row = \intval($form_vars['row']);
        $record->column = \intval($form_vars['column']);
        $bed = $this->app->db->beds->find_by_id(new ObjectId($form_vars['bed']));
        $record->bed = $bed;
        $seed = $this->app->db->seeds->find_by_id(new ObjectId($form_vars['seed']));
        $record->seed = $seed;
        $record->status = 'Active';
        $record->is_transplant = $form_vars['is_transplant'] === 'Yes';
        $record->notes = $form_vars['notes'];
        $record->tray_id = $form_vars['tray_id'];

        $record->create();

        $this->app->templates->addData([
            'toast' => "Created new planting (<a href=\"/plantings/{$record->get_id()}\">{$seed->display_string()}</a>)"
        ]);

        echo $this->app->templates->render(
            'plantings::new',
            [
                'seeds' => $this->get_seed_select_data(),
                'beds' => $this->get_bed_select_data(),
            ],
        );
    }

    public function plantings_post() {
        switch ($this->app->request->POST['action']) {
            case 'delete_planting':
                $this->plantings_delete();
                break;
        }

        $this->plantings();
    }

    private function plantings_delete() {
        $planting = $this->app->db->plantings->find_by_id($this->app->request->POST['planting_id']);

        if (\is_null($planting)) {
            $toast_msg = "Planting does not exist with ID {$this->app->request->POST['planting_id']}";
        } else {
            try {
                $planting->delete();
            } catch (\Exception $e) {
                $toast_msg = 'Error deleting seed: '.$e;
            }

            $toast_msg = "Planting deleted";
        }

        $this->app->templates->addData(['toast' => $toast_msg]);
    }

    public function plantings_edit_get($id) {
        $planting = $this->app->db->plantings->find_by_id($id);

        echo $this->app->templates->render('plantings::edit',
            [
                'planting' => $planting,
                'seeds' => $this->get_seed_select_data(),
                'beds' => $this->get_bed_select_data(),
            ]
        );
    }

    public function plantings_edit_post($id) {
        $form_vars = $this->app->request->POST;
        $record = $this->app->db->plantings->find_by_id($id);

        $record->row = \intval($form_vars['row']);
        $record->column = \intval($form_vars['column']);
        $bed = $this->app->db->beds->find_by_id(new ObjectId($form_vars['bed']));
        $record->bed = $bed;
        $seed = $this->app->db->seeds->find_by_id(new ObjectId($form_vars['seed']));
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

        $record->save();

        $this->app->templates->addData([
            'toast' => "Saved planting (<a href=\"/plantings/{$record->get_id()}\">{$seed->display_string()}</a>)"
        ]);

        echo $this->app->templates->render('plantings::edit',
            [
                'planting' => $record,
                'seeds' => $this->get_seed_select_data(),
                'beds' => $this->get_bed_select_data(),
            ]
        );
    }

    public function plantings_gallery_get($id) {
        $dir = $this->app->request->GET['dir'] ?? 'desc';
        $planting = $this->app->db->plantings->find_by_id($id);

        $log_dir = $dir === 'asc' ? 1 : -1;
        $logs = $this->app->db->logs->get_planting_logs($id, 'date', $log_dir);

        echo $this->app->templates->render('plantings::gallery',
            [
                'planting' => $planting,
                'logs' => $logs,
                'sort_dir' => $dir === 'desc' ? 'asc' : 'desc',
            ]
        );
    }
}
