<?php
declare(strict_types=1);
namespace Garden\Controllers;

use Garden\Application;
use Onesimus\Router\Http\Request;
use Onesimus\Router\Attr\Route;
use Onesimus\Router\Attr\Route404;

class IndexController {
    #[Route('get', '/')]
    public function index(Request $request, Application $app) {
        $plantings = $app->db->plantings->find_multiple(
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

        $logs = $app->db->logs->find_multiple(
            [],
            ['limit' => 15, 'sort' => ['date' => -1]],
        );

        $beds = $app->db->beds->get_all();
        $bed_plantings = [];

        foreach ($beds as $bed) {
            $bed_plantings[$bed->get_id()] = $app->db->plantings->get_in_bed($bed->get_id_obj());
        }

        echo $app->templates->render(
            'index',
            [
                'plantings' => $plantings,
                'logs' => $logs,
                'beds' => $beds,
                'bed_plantings' => $bed_plantings,
            ],
        );
    }

    #[Route404]
    public function error404(Request $request, Application $app) {
        echo $app->templates->render('404');
    }
}
