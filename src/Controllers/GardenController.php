<?php

declare(strict_types=1);

namespace Garden\Controllers;

use Garden\Models;
use Garden\Application;
use Onesimus\Router\Http\Request;
use Onesimus\Router\Attr\Route;
use Onesimus\Router\Attr\Filter;
use MongoDB\BSON\ObjectId;

class GardenController
{
    #[Route('get', '/gardens')]
    public function gardens(Request $request, Application $app)
    {
        $sort_prop = $request->GET['sort_by'] ?? 'name';
        $sort_dir = $request->GET['sort_dir'] ?? 1;
        $sort_dir = intval($sort_dir);
        if ($sort_dir < -1 || $sort_dir > 1) {
            $sort_dir = 1;
        }

        $all_gardens = $app->db->gardens->get_all(
            $sort_prop,
            $sort_dir,
        );

        echo $app->templates->render(
            'gardens::index',
            [
                'all_gardens' => $all_gardens,
                'sort_by' => $sort_prop,
                'sort_dir' => $sort_dir,
            ],
        );
    }

    #[Route('get', '/gardens/{id}')]
    public function gardens_view_get(Request $request, Application $app, string $id)
    {
        $garden = $app->db->gardens->find_by_id($id);

        echo $app->templates->render(
            'gardens::view',
            [
                'garden' => $garden,
                'beds' => $garden->get_beds($app),
            ]
        );
    }

    #[Filter('LoginRequired')]
    #[Route('get', '/gardens/new')]
    public function gardens_new_get(Request $request, Application $app)
    {
        echo $app->templates->render('gardens::new');
    }

    #[Filter('LoginRequired')]
    #[Route('post', '/gardens/new')]
    public function gardens_new_post(Request $request, Application $app)
    {
        $form_vars = $request->POST;

        $record = new Models\Garden();

        $record->added = new \DateTimeImmutable();
        $record->name = $form_vars['name'];
        $record->rows = \intval($form_vars['rows']);
        $record->cols = \intval($form_vars['cols']);
        $record->notes = $form_vars['notes'];

        $app->db->gardens->create($record);

        $app->templates->addData([
            'toast' => "Created new garden (<a href=\"/gardens/{$record->get_id()}\">{$record->name}</a>)"
        ]);

        echo $app->templates->render('gardens::new');
    }

    #[Filter('LoginRequired')]
    #[Route('post', '/gardens')]
    public function gardens_post(Request $request, Application $app)
    {
        switch ($request->POST['action']) {
            case 'delete_garden':
                $this->gardens_delete($request, $app);
                break;
        }

        $this->gardens($request, $app);
    }

    #[Filter('LoginRequired')]
    private function gardens_delete(Request $request, Application $app)
    {
        $garden = $app->db->gardens->find_by_id($request->POST['garden_id']);

        if (\is_null($garden)) {
            $toast_msg = "garden does not exist with ID {$request->POST['garden_id']}";
        } else {
            try {
                $app->db->gardens->delete($garden);
            } catch (\Exception $e) {
                $toast_msg = 'Error deleting garden: '.$e;
            }

            $toast_msg = "garden deleted";
        }

        $app->templates->addData(['toast' => $toast_msg]);
    }

    #[Filter('LoginRequired')]
    #[Route('get', '/gardens/edit/{id}')]
    public function gardens_edit_get(Request $request, Application $app, string $id)
    {
        $garden = $app->db->gardens->find_by_id($id);

        echo $app->templates->render('gardens::edit', ['garden' => $garden]);
    }

    #[Filter('LoginRequired')]
    #[Route('post', '/gardens/edit/{id}')]
    public function gardens_edit_post(Request $request, Application $app, string $id)
    {
        $form_vars = $request->POST;
        $record = $app->db->gardens->find_by_id($id);

        $record->name = $form_vars['name'];
        $record->rows = \intval($form_vars['rows']);
        $record->cols = \intval($form_vars['cols']);
        $record->notes = $form_vars['notes'];

        if (\array_key_exists('hide_from_home', $form_vars)) {
            $record->hide_from_home = $form_vars['hide_from_home'] === 'on';
        } else {
            $record->hide_from_home = false;
        }

        $app->db->gardens->save($record);

        $app->templates->addData([
            'toast' => "Saved garden ({$record->name})"
        ]);

        $this->gardens_view_get($request, $app, $id);
    }
}