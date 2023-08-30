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

class LogController {
    #[Route('get', '/logs')]
    public function logs(Request $request, Application $app) {
        $all_logs = $app->db->logs->get_all(
            'date',
            -1,
        );

        echo $app->templates->render(
            'logs::index',
            [
                'all_logs' => $all_logs,
            ],
        );
    }

    #[Route('get', '/logs/{id}')]
    public function logs_view_get(Request $request, Application $app, string $id) {
        $log = $app->db->logs->find_by_id($id);

        echo $app->templates->render('logs::view',
            [
                'log' => $log,
            ]
        );
    }

    #[Filter('LoginRequired')]
    #[Route('get', '/logs/new')]
    public function logs_new_get(Request $request, Application $app) {
        $form_vars = $request->GET;
        $preselect_id = $form_vars['planting'] ?? '';

        echo $app->templates->render(
            'logs::new',
            ['plantings' => $this->get_planting_select_data(), 'select_planting' => $preselect_id],
        );
    }

    #[Filter('LoginRequired')]
    #[Route('post', '/logs/new')]
    public function logs_new_post(Request $request, Application $app) {
        $form_vars = $request->POST;

        $record = new Models\Log($app->db, null);

        $record->date = new \DateTimeImmutable();
        if ($form_vars['planting'] !== 'All') {
            $planting = $app->db->plantings->find_by_id(new ObjectId($form_vars['planting']));
            $record->planting = $planting;
        }
        $record->notes = $form_vars['notes'];
        $record->time_of_day = $form_vars['time_of_day'];
        if (\str_contains($form_vars['image_files'], '.png')) {
            $record->image_files = \explode(";", $form_vars['image_files']);
        }

        $record->create();

        $app->templates->addData([
            'toast' => "Created new <a href=\"/logs/{$record->get_id()}\">log</a>"
        ]);

        echo $app->templates->render(
            'logs::new',
            ['plantings' => $this->get_planting_select_data()],
        );
    }

    private function get_planting_select_data(Application $app): array {
        $plantings = $app->db->plantings->get_all([], 'date');
        $planting_data = [];
        foreach ($plantings as $planting) {
            $planting_data []= [
                'name' => $planting->display_string(),
                'id' => $planting->get_id(),
            ];
        }
        return $planting_data;
    }

    #[Filter('LoginRequired')]
    #[Route('post', '/logs')]
    public function logs_post(Request $request, Application $app) {
        switch ($request->POST['action']) {
            case 'delete_log':
                $this->logs_delete($request, $app);
                break;
        }

        $this->logs($request, $app);
    }

    private function logs_delete(Request $request, Application $app) {
        $log = $app->db->logs->find_by_id($request->POST['log_id']);

        if (\is_null($log)) {
            $toast_msg = "Log does not exist with ID {$request->POST['log_id']}";
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

        $app->templates->addData(['toast' => $toast_msg]);
    }

    #[Filter('LoginRequired')]
    #[Route('get', '/logs/edit/{id}')]
    public function logs_edit_get(Request $request, Application $app, string $id) {
        $log = $app->db->logs->find_by_id($id);

        echo $app->templates->render(
            'logs::edit',
            [
                'log' => $log,
                'plantings' => $this->get_planting_select_data($app),
            ],
        );
    }

    #[Filter('LoginRequired')]
    #[Route('post', '/logs/edit/{id}')]
    public function logs_edit_post(Request $request, Application $app, string $id) {
        $form_vars = $request->POST;
        $record = $app->db->logs->find_by_id($id);

        if ($form_vars['planting'] !== 'All') {
            $planting = $app->db->plantings->find_by_id(new ObjectId($form_vars['planting']));
            $record->planting = $planting;
        }
        $record->notes = $form_vars['notes'];
        $record->time_of_day = $form_vars['time_of_day'];

        $record->save();

        $app->templates->addData([
            'toast' => "Saved log ({$record->date->format('Y-m-d H:i:s')})",
        ]);

        echo $app->templates->render(
            'logs::edit',
            [
                'log' => $record,
                'plantings' => $this->get_planting_select_data($app),
            ],
        );
    }
}
