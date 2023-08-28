<?php
declare(strict_types=1);
namespace Root\Garden\Controllers;

use Root\Garden\Models;
use Root\Garden\Application;

class SeedController {
    private Application $app;

    public function __construct($app) {
        $this->app = $app;
    }

    public function seeds() {
        $sort_prop = $this->app->request->GET['sort_by'] ?? 'common_name';
        $sort_dir = $this->app->request->GET['sort_dir'] ?? 1;
        $sort_dir = intval($sort_dir);
        if ($sort_dir < -1 || $sort_dir > 1) {
            $sort_dir = 1;
        }

        echo $this->app->templates->render(
            'seeds::index',
            [
                'allSeeds' => $this->app->db->seeds->get_all($sort_prop, $sort_dir),
                'sort_by' => $sort_prop,
                'sort_dir' => $sort_dir,
            ],
        );
    }

    public function wishlist() {
        $sort_prop = $this->app->request->GET['sort_by'] ?? 'common_name';
        $sort_dir = $this->app->request->GET['sort_dir'] ?? 1;
        $sort_dir = intval($sort_dir);
        if ($sort_dir < -1 || $sort_dir > 1) {
            $sort_dir = 1;
        }

        echo $this->app->templates->render(
            'seeds::index',
            [
                'allSeeds' => $this->app->db->seeds->get_all_wishlist($sort_prop, $sort_dir),
                'sort_by' => $sort_prop,
                'sort_dir' => $sort_dir,
            ],
        );
    }

    public function seeds_view_get($id) {
        $seed = $this->app->db->seeds->find_by_id($id);

        echo $this->app->templates->render('seeds::view',
            [
                'seed' => $seed,
            ]
        );
    }

    public function seeds_new_get() {
        echo $this->app->templates->render('seeds::new');
    }

    public function seeds_new_post() {
        $form_vars = $this->app->request->POST;

        $seed = new Models\Seed($this->app->db, null);

        $seed->added = new \DateTimeImmutable();
        $seed->type = $form_vars['seed_type'];
        $seed->variety = $form_vars['variety_name'];
        $seed->days_to_maturity = intval($form_vars['days_to_maturity']);
        $seed->days_to_germination = intval($form_vars['days_to_germination']);
        $seed->is_heirloom = $form_vars['is_heirloom'] == 'Yes';
        $seed->sun = $form_vars['sun_amt'];
        $seed->season = $form_vars['growing_season'] ?? ['Summer'];
        $seed->characteristics = $form_vars['other_charact'] ?? [];
        $seed->is_hybrid = $form_vars['is_hybrid'] == 'Yes';
        $seed->source = $form_vars['source'];
        $seed->link = $form_vars['source_link'];
        $seed->notes = $form_vars['notes'];
        $seed->on_wishlist = \array_key_exists('on_wishlist', $form_vars);

        switch ($seed->type) {
            case 'Vegetable':
                $seed->common_name = $form_vars['seed_vegetable_name'];
                break;
            case 'Herb':
                $seed->common_name = $form_vars['seed_herb_name'];
                break;
            case 'Fruit':
                $seed->common_name = $form_vars['seed_fruit_name'];
                break;
            case 'Flower':
                $seed->common_name = $form_vars['seed_flower_name'];
                break;
        }

        $seed->create();

        $this->app->templates->addData(['toast' => "Saved seed (<a href=\"/seeds/{$seed->get_id()}\">{$seed->display_string()}</a>)"]);
        echo $this->app->templates->render('seeds::new');
    }

    public function seeds_post() {
        switch ($this->app->request->POST['action']) {
            case 'delete_seed':
                $this->seeds_delete();
                break;
        }
    }

    private function seeds_delete() {
        $seed = $this->app->db->seeds->find_by_id($this->app->request->POST['seed_id']);

        if (\is_null($seed)) {
            $toast_msg = "Seed does not exist with ID {$this->app->request->POST['seed_id']}";
        } else {
            $toast_msg = "Seed deleted ({$seed->common_name} - {$seed->variety})";

            try {
                $seed->delete();
            } catch (\Exception $e) {
                $toast_msg = 'Error deleting seed: '.$e;
            }
        }

        $this->app->templates->addData(['toast' => $toast_msg]);
        echo $this->app->templates->render('seeds::index',
            [
                'allSeeds' => $this->app->db->seeds->get_all(),
            ]
        );
    }

    public function seeds_edit_get($id) {
        $seed = $this->app->db->seeds->find_by_id($id);

        echo $this->app->templates->render('seeds::edit',
            [
                'seed' => $seed,
            ]
        );
    }

    public function seeds_edit_post($id) {
        $form_vars = $this->app->request->POST;
        $seed = $this->app->db->seeds->find_by_id($id);

        $seed->type = $form_vars['seed_type'];
        $seed->variety = $form_vars['variety_name'];
        $seed->days_to_maturity = intval($form_vars['days_to_maturity']);
        $seed->days_to_germination = intval($form_vars['days_to_germination']);
        $seed->is_heirloom = $form_vars['is_heirloom'] == 'Yes';
        $seed->sun = $form_vars['sun_amt'];
        $seed->season = $form_vars['growing_season'] ?? ['Summer'];
        $seed->characteristics = $form_vars['other_charact'] ?? [];
        $seed->is_hybrid = $form_vars['is_hybrid'] == 'Yes';
        $seed->source = $form_vars['source'];
        $seed->link = $form_vars['source_link'];
        $seed->notes = $form_vars['notes'];
        $seed->on_wishlist = \array_key_exists('on_wishlist', $form_vars);

        switch ($seed->type) {
            case 'Vegetable':
                $seed->common_name = $form_vars['seed_vegetable_name'];
                break;
            case 'Herb':
                $seed->common_name = $form_vars['seed_herb_name'];
                break;
            case 'Fruit':
                $seed->common_name = $form_vars['seed_fruit_name'];
                break;
            case 'Flower':
                $seed->common_name = $form_vars['seed_flower_name'];
                break;
        }

        $seed->save();

        $this->app->templates->addData(['toast' => "Saved seed (<a href=\"/seeds/{$seed->get_id()}\">{$seed->display_string()}</a>)"]);
        echo $this->app->templates->render('seeds::view',
            [
                'seed' => $seed,
            ]
        );
    }
}
