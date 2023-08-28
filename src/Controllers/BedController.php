<?php
declare(strict_types=1);
namespace Root\Garden\Controllers;

use Root\Garden\Models;
use Root\Garden\Application;
use MongoDB\BSON\ObjectId;

class BedController {
    private Application $app;

    public function __construct($app) {
        $this->app = $app;
    }

    public function beds() {
        $sort_prop = $this->app->request->GET['sort_by'] ?? 'name';
        $sort_dir = $this->app->request->GET['sort_dir'] ?? 1;
        $sort_dir = intval($sort_dir);
        if ($sort_dir < -1 || $sort_dir > 1) {
            $sort_dir = 1;
        }

        $all_beds = $this->app->db->beds->get_all(
            $sort_prop,
            $sort_dir,
        );

        echo $this->app->templates->render(
            'beds::index',
            [
                'all_beds' => $all_beds,
                'sort_by' => $sort_prop,
                'sort_dir' => $sort_dir,
            ],
        );
    }

    public function beds_view_get($id) {
        $bed = $this->app->db->beds->find_by_id($id);

        echo $this->app->templates->render('beds::view',
            [
                'bed' => $bed,
            ]
        );
    }

    public function beds_new_get() {
        echo $this->app->templates->render('beds::new');
    }

    public function beds_new_post() {
        $form_vars = $this->app->request->POST;

        $record = new Models\Bed($this->app->db, null);

        $record->added = new \DateTimeImmutable();
        $record->name = $form_vars['name'];
        $record->rows = \intval($form_vars['rows']);
        $record->cols = \intval($form_vars['cols']);
        $record->notes = $form_vars['notes'];

        $record->create();

        $this->app->templates->addData([
            'toast' => "Created new bed (<a href=\"/beds/{$record->get_id()}\">{$record->name}</a>)"
        ]);

        echo $this->app->templates->render('beds::new');
    }

    public function beds_post() {
        switch ($this->app->request->POST['action']) {
            case 'delete_bed':
                $this->beds_delete();
                break;
        }

        $this->beds();
    }

    private function beds_delete() {
        $bed = $this->app->db->beds->find_by_id($this->app->request->POST['bed_id']);

        if (\is_null($bed)) {
            $toast_msg = "Bed does not exist with ID {$this->app->request->POST['bed_id']}";
        } else {
            try {
                $bed->delete();
            } catch (\Exception $e) {
                $toast_msg = 'Error deleting bed: '.$e;
            }

            $toast_msg = "Bed deleted";
        }

        $this->app->templates->addData(['toast' => $toast_msg]);
    }

    public function beds_edit_get($id) {
        $bed = $this->app->db->beds->find_by_id($id);

        echo $this->app->templates->render('beds::edit', ['bed' => $bed]);
    }

    public function beds_edit_post($id) {
        $form_vars = $this->app->request->POST;
        $record = $this->app->db->beds->find_by_id($id);

        $record->name = $form_vars['name'];
        $record->rows = \intval($form_vars['rows']);
        $record->cols = \intval($form_vars['cols']);
        $record->notes = $form_vars['notes'];

        $record->save();

        $this->app->templates->addData([
            'toast' => "Saved bed ({$record->name})"
        ]);

        echo $this->app->templates->render('beds::edit', ['bed' => $record]);
    }
}
