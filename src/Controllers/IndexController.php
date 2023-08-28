<?php
declare(strict_types=1);
namespace Root\Garden\Controllers;

use Root\Garden\Application;

class IndexController {
    private Application $app;

    public function __construct(Application $app) {
        $this->app = $app;
    }

    public function index() {
        $plantings = $this->app->db->plantings->find_multiple(
            [
                'status' => [
                    '$nin' => [
                        'Harvested',
                        'Failed',
                        'Transplanted',
                    ],
                ],
            ],
            ['sort' => ['date' => 1]],
        );

        $logs = $this->app->db->logs->find_multiple(
            [],
            ['$limit' => 15, 'sort' => ['date' => -1]],
        );

        $beds = $this->app->db->beds->get_all();
        $bed_plantings = [];

        foreach ($beds as $bed) {
            $bed_plantings[$bed->get_id()] = $this->app->db->plantings->get_in_bed($bed->get_id_obj());
        }

        echo $this->app->templates->render(
            'index',
            [
                'plantings' => $plantings,
                'logs' => $logs,
                'beds' => $beds,
                'bed_plantings' => $bed_plantings,
            ],
        );
    }

    public function error404() {
        echo $this->app->templates->render('404');
    }
}
