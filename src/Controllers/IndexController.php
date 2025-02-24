<?php

declare(strict_types=1);

namespace Garden\Controllers;

use Garden\Application;
use Onesimus\Router\Http\Request;
use Onesimus\Router\Attr\Route;
use Onesimus\Router\Attr\Route404;

class IndexController
{
    #[Route('get', '/')]
    public function index(Request $request, Application $app)
    {
        $plantings = $app->db->plantings->find_multiple(
            [
                'status' => [
                    '$nin' => [
                        'Harvested',
                        'Failed',
                        'Transplanted',
                        'Finished',
                        'Planned',
                    ],
                ],
            ],
            ['sort' => ['date' => -1]],
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

        if (\array_key_exists('login', $request->get)) {
            $app->templates->addData([
                'toast' => 'Logged in'
            ]);
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

    #[Route('get', '/login')]
    public function login(Request $request, Application $app)
    {
        echo $app->templates->render('login');
    }

    #[Route('post', '/login')]
    public function login_post(Request $request, Application $app)
    {
        $formdata = $request->post;

        if (
            !\array_key_exists('username', $formdata) ||
            !\array_key_exists('password', $formdata)
        ) {
            $app->templates->addData([
                'toast' => "Incorrect username or password"
            ]);
            $this->login($request, $app);
            return;
        }

        $user = $app->db->users->find_one('username', $formdata['username']);
        if (!$user) {
            $app->templates->addData([
                'toast' => "Incorrect username or password"
            ]);
            $this->login($request, $app);
            return;
        }

        if (!$user->valid_password($formdata['password'])) {
            $app->templates->addData([
                'toast' => "Incorrect username or password"
            ]);
            $this->login($request, $app);
            return;
        }

        $_SESSION['logged_in'] = true;
        \header('Location: /?login=success');
        \http_response_code(303);
    }

    #[Route('get', '/logout')]
    public function logout(Request $request, Application $app)
    {
        $_SESSION['logged_in'] = false;
        $app->templates->addData([
            'toast' => "Logged out"
        ]);
        $this->login($request, $app);
    }

    #[Route404]
    public function error404(Request $request, Application $app)
    {
        echo $app->templates->render('404');
    }
}
