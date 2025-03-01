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

const ACTIVE_PLANTING_FILTER = [
    'status' => [
        '$nin' => [
            'Harvested',
            'Failed',
            'Transplanted',
        ],
    ]
];

class LogController
{
    #[Route('get', '/logs')]
    public function logs(Request $request, Application $app)
    {
        $get = $request->GET;
        $start_date = null;
        $end_date = null;

        if (\array_key_exists('start_date', $get)) {
            $start_date = (new \DateTimeImmutable($get['start_date']));
        } else {
            $start_date = (new \DateTimeImmutable())->sub(new \DateInterval('P1M'));
        }
        $start_date = $start_date->setTime(0, 0);

        if (\array_key_exists('end_date', $get)) {
            $end_date = (new \DateTimeImmutable($get['end_date']));
        } else {
            $end_date = $start_date->add(new \DateInterval('P1M'));
        }
        $end_date = $end_date->setTime(23, 59);

        $logs = $app->db->logs->get_logs_date(
            $start_date,
            $end_date,
            'date',
            -1,
        );

        echo $app->templates->render(
            'logs::index',
            [
                'all_logs' => $logs,
                'start_date' => $start_date->format('Y-m-d'),
                'end_date' => $end_date->format('Y-m-d'),
            ],
        );
    }

    #[Route('get', '/logs/monthly')]
    public function logs_monthly(Request $request, Application $app)
    {
        $get = $request->GET;

        $month = $get['month'] ?? \date('m');
        $year = $get['year'] ?? \date('Y');

        $start_date = null;
        $end_date = null;

        if (\array_key_exists('month', $get)) {
            $start_date = (new \DateTimeImmutable())->setDate(\intval($year), \intval($get['month']), 1);
        } else {
            $start_date = (new \DateTimeImmutable())->setDate(\intval($year), \intval($month), 1);
        }
        $start_date = $start_date->setTime(0, 0);

        $end_date = $start_date->add(new \DateInterval('P1M'));
        $end_date = $end_date->setTime(0, 0);

        $logs = $app->db->logs->get_logs_date(
            $start_date,
            $end_date,
            'date',
            -1,
        );

        $items = [];
        foreach ($logs as $log) {
            $items []= [
                'date' => $log->date,
                'span_title' => $log->notes,
                'link' => "/logs/{$log->get_id()}",
                'title' => $log->display_string(),
            ];
        }

        echo $app->templates->render(
            'logs::monthly',
            [
                'items' => $items,
                'month' => $month,
                'year' => $year,
                'start_day' => $start_date->format('w'),
            ],
        );
    }

    #[Route('get', '/logs/{id}')]
    public function logs_view_get(Request $request, Application $app, string $id)
    {
        $log = $app->db->logs->find_by_id($id);

        echo $app->templates->render(
            'logs::view',
            [
                'log' => $log,
            ]
        );
    }

    #[Filter('LoginRequired')]
    #[Route('get', '/logs/new')]
    public function logs_new_get(Request $request, Application $app)
    {
        $form_vars = $request->GET;
        $preselect_id = $form_vars['planting'] ?? '';
        $selected_plantings = $form_vars['selected'] ?? '';

        echo $app->templates->render(
            'logs::new',
            [
                'plantings' => $this->get_planting_select_data($app, ACTIVE_PLANTING_FILTER),
                'select_planting' => $preselect_id,
                'rest_of_plantings' => $selected_plantings,
                'planting_tags' => $app->db->plantings->get_all_tags(),
            ],
        );
    }

    #[Filter('LoginRequired')]
    #[Route('post', '/logs/new')]
    public function logs_new_post(Request $request, Application $app)
    {
        $form_vars = $request->POST;

        $preselect_id = '';
        $selected_plantings = $form_vars['selected'] ?? '';
        if ($selected_plantings) {
            $plantings = \explode(',', $selected_plantings);
            $preselect_id = \array_shift($plantings);
            $selected_plantings = \implode(',', $plantings);
        }

        $record = new Models\Log();

        $record->date = new \DateTimeImmutable($form_vars['log_date'] ?? 'now');
        if ($form_vars['planting'] !== 'All') {
            $planting = $app->db->plantings->find_by_id(new ObjectId($form_vars['planting']));
            $record->planting = $planting;
        }
        $record->notes = $form_vars['notes'];
        $record->planting_tag = $form_vars['planting_tag'];

        $now_hour = \intval($record->date->format('H'));
        if ($now_hour < 12) {
            $record->time_of_day = "Morning";
        } elseif ($now_hour < 18) {
            $record->time_of_day = 'Afternoon';
        } else {
            $record->time_of_day = 'Evening';
        }

        if (\str_contains($form_vars['image_files'], '.png')) {
            $record->image_files = \explode(";", $form_vars['image_files']);
        }

        $app->db->logs->create($record);

        $app->templates->addData([
            'toast' => "Created new <a href=\"/logs/{$record->get_id()}\">log</a>"
        ]);

        echo $app->templates->render(
            'logs::new',
            [
                'plantings' => $this->get_planting_select_data($app, ACTIVE_PLANTING_FILTER),
                'select_planting' => $preselect_id,
                'rest_of_plantings' => $selected_plantings,
                'planting_tags' => $app->db->plantings->get_all_tags(),
            ],
        );
    }

    private function get_planting_select_data(Application $app, array $filter = []): array
    {
        $plantings = $app->db->plantings->get_all('date', 1, $filter);
        $planting_data = [];
        foreach ($plantings as $planting) {
            $planting_data[] = [
                'name' => $planting->display_string(),
                'id' => $planting->get_id(),
            ];
        }
        return $planting_data;
    }

    #[Filter('LoginRequired')]
    #[Route('post', '/logs')]
    public function logs_post(Request $request, Application $app)
    {
        switch ($request->POST['action']) {
            case 'delete_log':
                $this->logs_delete($request, $app);
                break;
        }

        $this->logs($request, $app);
    }

    private function logs_delete(Request $request, Application $app)
    {
        $log = $app->db->logs->find_by_id($request->POST['log_id']);

        if (\is_null($log)) {
            $toast_msg = "Log does not exist with ID {$request->POST['log_id']}";
        } else {
            try {
                $files = $log->image_files;
                $app->db->logs->delete($log);

                foreach ($files as $file) {
                    \unlink("../uploads/$file");
                }
            } catch (\Exception $e) {
                $toast_msg = 'Error deleting log: ' . $e;
            }

            $toast_msg = "Log deleted";
        }

        $app->templates->addData(['toast' => $toast_msg]);
    }

    #[Filter('LoginRequired')]
    #[Route('get', '/logs/edit/{id}')]
    public function logs_edit_get(Request $request, Application $app, string $id)
    {
        $log = $app->db->logs->find_by_id($id);

        echo $app->templates->render(
            'logs::edit',
            [
                'log' => $log,
                'plantings' => $this->get_planting_select_data($app, ACTIVE_PLANTING_FILTER),
                'planting_tags' => $app->db->plantings->get_all_tags(),
            ],
        );
    }

    #[Filter('LoginRequired')]
    #[Route('post', '/logs/edit/{id}')]
    public function logs_edit_post(Request $request, Application $app, string $id)
    {
        $form_vars = $request->POST;
        $record = $app->db->logs->find_by_id($id);

        if ($form_vars['planting'] !== 'All') {
            $planting = $app->db->plantings->find_by_id(new ObjectId($form_vars['planting']));
            $record->planting = $planting;
        }
        $record->notes = $form_vars['notes'];
        $record->time_of_day = $form_vars['time_of_day'];
        $record->planting_tag = $form_vars['planting_tag'];

        $app->db->logs->save($record);

        $app->templates->addData([
            'toast' => "Saved log ({$record->date->format('Y-m-d H:i:s')})",
        ]);

        echo $app->templates->render(
            'logs::edit',
            [
                'log' => $record,
                'plantings' => $this->get_planting_select_data($app, ACTIVE_PLANTING_FILTER),
            ],
        );
    }

    #[Route('get', '/logs/gallery')]
    public function logs_gallery(Request $request, Application $app)
    {
        $get = $request->GET;

        $start_date = null;
        $end_date = null;

        if (\array_key_exists('start_date', $get)) {
            $start_date = (new \DateTimeImmutable($get['start_date']))->setTime(0, 0);
        } else {
            $start_date = (new \DateTimeImmutable())->sub(new \DateInterval('P1M'));
        }

        if (\array_key_exists('end_date', $get)) {
            $end_date = (new \DateTimeImmutable($get['end_date']))->setTime(23, 59);
        } else {
            $end_date = $start_date->add(new \DateInterval('P1M'));
        }

        $logs = $app->db->logs->get_logs_date(
            $start_date,
            $end_date,
            'date',
            -1,
        );

        echo $app->templates->render(
            'logs::photos',
            [
                'logs' => $logs,
                'start_date' => $start_date->format('Y-m-d'),
                'end_date' => $end_date->format('Y-m-d'),
            ],
        );
    }
}
