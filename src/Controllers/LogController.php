<?php
declare(strict_types=1);
namespace Root\Garden\Controllers;

use Root\Garden\Models;
use Root\Garden\Application;
use MongoDB\BSON\ObjectId;

class LogController {
    private Application $app;

    public function __construct($app) {
        $this->app = $app;
    }

    public function logs() {
        $all_logs = $this->app->db->logs->get_all(
            'date',
            -1,
        );

        echo $this->app->templates->render(
            'logs::index',
            [
                'all_logs' => $all_logs,
            ],
        );
    }

    public function logs_view_get($id) {
        $log = $this->app->db->logs->find_by_id($id);

        echo $this->app->templates->render('logs::view',
            [
                'log' => $log,
            ]
        );
    }

    public function logs_new_get() {
        $form_vars = $this->app->request->GET;
        $preselect_id = $form_vars['planting'] ?? '';

        echo $this->app->templates->render(
            'logs::new',
            ['plantings' => $this->get_planting_select_data(), 'select_planting' => $preselect_id],
        );
    }

    public function logs_new_post() {
        $form_vars = $this->app->request->POST;

        $record = new Models\Log($this->app->db, null);

        $record->date = new \DateTimeImmutable();
        if ($form_vars['planting'] !== 'All') {
            $planting = $this->app->db->plantings->find_by_id(new ObjectId($form_vars['planting']));
            $record->planting = $planting;
        }
        $record->notes = $form_vars['notes'];
        $record->time_of_day = $form_vars['time_of_day'];
        if (\str_contains($form_vars['image_files'], '.png')) {
            $record->image_files = \explode(";", $form_vars['image_files']);
        }

        $record->create();

        $this->app->templates->addData([
            'toast' => "Created new <a href=\"/logs/{$record->get_id()}\">log</a>"
        ]);

        echo $this->app->templates->render(
            'logs::new',
            ['plantings' => $this->get_planting_select_data()],
        );
    }

    private function get_planting_select_data(): array {
        $plantings = $this->app->db->plantings->get_all([], 'date');
        $planting_data = [];
        foreach ($plantings as $planting) {
            $planting_data []= [
                'name' => $planting->display_string(),
                'id' => $planting->get_id(),
            ];
        }
        return $planting_data;
    }

    public function logs_post() {
        switch ($this->app->request->POST['action']) {
            case 'delete_log':
                $this->logs_delete();
                break;
        }

        $this->logs();
    }

    private function logs_delete() {
        $log = $this->app->db->logs->find_by_id($this->app->request->POST['log_id']);

        if (\is_null($log)) {
            $toast_msg = "Log does not exist with ID {$this->app->request->POST['log_id']}";
        } else {
            try {
                $files = $log->image_files;
                $log->delete();

                foreach($files as $file) {
                    \unlink("../uploads/$file");
                }
            } catch (\Exception $e) {
                $toast_msg = 'Error deleting log: '.$e;
            }

            $toast_msg = "Log deleted";
        }

        $this->app->templates->addData(['toast' => $toast_msg]);
    }

    public function logs_edit_get($id) {
        $log = $this->app->db->logs->find_by_id($id);

        echo $this->app->templates->render(
            'logs::edit',
            [
                'log' => $log,
                'plantings' => $this->get_planting_select_data(),
            ],
        );
    }

    public function logs_edit_post($id) {
        $form_vars = $this->app->request->POST;
        $record = $this->app->db->logs->find_by_id($id);

        if ($form_vars['planting'] !== 'All') {
            $planting = $this->app->db->plantings->find_by_id(new ObjectId($form_vars['planting']));
            $record->planting = $planting;
        }
        $record->notes = $form_vars['notes'];
        $record->time_of_day = $form_vars['time_of_day'];

        $record->save();

        $this->app->templates->addData([
            'toast' => "Saved log ({$record->date->format('Y-m-d H:i:s')})",
        ]);

        echo $this->app->templates->render(
            'logs::edit',
            [
                'log' => $record,
                'plantings' => $this->get_planting_select_data(),
            ],
        );
    }
}
