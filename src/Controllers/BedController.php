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
            ]
        );
    }

    #[Filter('LoginRequired')]
    #[Route('get', '/beds/new')]
    public function beds_new_get(Request $request, Application $app)
    {
        echo $app->templates->render('beds::new');
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

        $app->db->beds->create($record);

        $app->templates->addData([
            'toast' => "Created new bed (<a href=\"/beds/{$record->get_id()}\">{$record->name}</a>)"
        ]);

        echo $app->templates->render('beds::new');
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

        echo $app->templates->render('beds::edit', ['bed' => $bed]);
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

        $app->db->beds->save($record);

        $app->templates->addData([
            'toast' => "Saved bed ({$record->name})"
        ]);

        echo $app->templates->render('beds::edit', ['bed' => $record]);
    }
}
