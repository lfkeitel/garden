<?php

declare(strict_types=1);

namespace Garden\Controllers;

use Garden\Models;
use Garden\Application;
use Onesimus\Router\Http\Request;
use Onesimus\Router\Attr\Route;
use Onesimus\Router\Attr\Filter;
use MongoDB\BSON\ObjectId;

class BedController
{
    #[Route('get', '/beds')]
    public function beds(Request $request, Application $app)
    {
        $sort_prop = $request->GET['sort_by'] ?? 'name';
        $sort_dir = $request->GET['sort_dir'] ?? 1;
        $sort_dir = intval($sort_dir);
        if ($sort_dir < -1 || $sort_dir > 1) {
            $sort_dir = 1;
        }

        $all_beds = $app->db->beds->get_all(
            $sort_prop,
            $sort_dir,
        );

        echo $app->templates->render(
            'beds::index',
            [
                'all_beds' => $all_beds,
                'sort_by' => $sort_prop,
                'sort_dir' => $sort_dir,
            ],
        );
    }

    #[Route('get', '/beds/{id}')]
    public function beds_view_get(Request $request, Application $app, string $id)
    {
        $bed = $app->db->beds->find_by_id($id);

        echo $app->templates->render(
            'beds::view',
            [
                'bed' => $bed,
                'plantings' => $bed->get_plantings($app),
            ]
        );
    }

    private function get_garden_select_data(Application $app): array
    {
        $gardens = $app->db->gardens->get_all('name');
        $garden_data = [];
        foreach ($gardens as $garden) {
            $garden_data[] = [
                'name' => $garden->display_string(),
                'id' => $garden->get_id(),
            ];
        }
        return $garden_data;
    }

    #[Filter('LoginRequired')]
    #[Route('get', '/beds/new')]
    public function beds_new_get(Request $request, Application $app)
    {
        echo $app->templates->render('beds::new', [
            'gardens' => $this->get_garden_select_data($app),
        ]);
    }

    #[Filter('LoginRequired')]
    #[Route('post', '/beds/new')]
    public function beds_new_post(Request $request, Application $app)
    {
        $form_vars = $request->POST;

        $record = new Models\Bed();

        $record->added = new \DateTimeImmutable();
        $record->name = $form_vars['name'];
        $record->rows = \intval($form_vars['rows']);
        $record->cols = \intval($form_vars['cols']);
        $record->notes = $form_vars['notes'];
        $record->garden = $app->db->gardens->find_by_id(new ObjectId($form_vars['garden']));

        $app->db->beds->create($record);

        $app->templates->addData([
            'toast' => "Created new bed (<a href=\"/beds/{$record->get_id()}\">{$record->name}</a>)"
        ]);

        $this->beds($request, $app);
    }

    #[Filter('LoginRequired')]
    #[Route('post', '/beds')]
    public function beds_post(Request $request, Application $app)
    {
        switch ($request->POST['action']) {
            case 'delete_bed':
                $this->beds_delete($request, $app);
                break;
        }

        $this->beds($request, $app);
    }

    #[Filter('LoginRequired')]
    private function beds_delete(Request $request, Application $app)
    {
        $bed = $app->db->beds->find_by_id($request->POST['bed_id']);

        if (\is_null($bed)) {
            $toast_msg = "Bed does not exist with ID {$request->POST['bed_id']}";
        } else {
            try {
                $app->db->beds->delete($bed);
            } catch (\Exception $e) {
                $toast_msg = 'Error deleting bed: '.$e;
            }

            $toast_msg = "Bed deleted";
        }

        $app->templates->addData(['toast' => $toast_msg]);
    }

    #[Filter('LoginRequired')]
    #[Route('get', '/beds/edit/{id}')]
    public function beds_edit_get(Request $request, Application $app, string $id)
    {
        $bed = $app->db->beds->find_by_id($id);

        echo $app->templates->render('beds::edit', [
            'bed' => $bed,
            'gardens' => $this->get_garden_select_data($app),
        ]);
    }

    #[Filter('LoginRequired')]
    #[Route('post', '/beds/edit/{id}')]
    public function beds_edit_post(Request $request, Application $app, string $id)
    {
        $form_vars = $request->POST;
        $record = $app->db->beds->find_by_id($id);

        $record->name = $form_vars['name'];
        $record->rows = \intval($form_vars['rows']);
        $record->cols = \intval($form_vars['cols']);
        $record->notes = $form_vars['notes'];
        $record->garden = $app->db->gardens->find_by_id(new ObjectId($form_vars['garden']));

        if (\array_key_exists('hide_from_home', $form_vars)) {
            $record->hide_from_home = $form_vars['hide_from_home'] === 'on';
        } else {
            $record->hide_from_home = false;
        }

        $app->db->beds->save($record);

        $app->templates->addData([
            'toast' => "Saved bed ({$record->name})"
        ]);

        $this->beds_view_get($request, $app, $id);
    }
}