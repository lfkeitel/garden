<?php
declare(strict_types=1);
namespace Garden\Controllers;

use Garden\Models;
use Garden\Application;
use Garden\Lib\LoginRequired;
use Onesimus\Router\Http\Request;
use Onesimus\Router\Attr\Route;
use Onesimus\Router\Attr\Filter;

class SeedController {
    #[Route('get', '/seeds')]
    public function seeds(Request $request, Application $app) {
        $sort_prop = $request->GET['sort_by'] ?? 'common_name';
        $sort_dir = $request->GET['sort_dir'] ?? 1;
        $sort_dir = intval($sort_dir);
        if ($sort_dir < -1 || $sort_dir > 1) {
            $sort_dir = 1;
        }

        echo $app->templates->render(
            'seeds::index',
            [
                'allSeeds' => $app->db->seeds->get_all($sort_prop, $sort_dir),
                'sort_by' => $sort_prop,
                'sort_dir' => $sort_dir,
            ],
        );
    }

    #[Route('get', '/wishlist')]
    public function wishlist(Request $request, Application $app) {
        $sort_prop = $request->GET['sort_by'] ?? 'common_name';
        $sort_dir = $request->GET['sort_dir'] ?? 1;
        $sort_dir = intval($sort_dir);
        if ($sort_dir < -1 || $sort_dir > 1) {
            $sort_dir = 1;
        }

        echo $app->templates->render(
            'seeds::index',
            [
                'allSeeds' => $app->db->seeds->get_all_wishlist($sort_prop, $sort_dir),
                'sort_by' => $sort_prop,
                'sort_dir' => $sort_dir,
            ],
        );
    }

    #[Route('get', '/seeds/{id}')]
    public function seeds_view_get(Request $request, Application $app, string $id) {
        $seed = $app->db->seeds->find_by_id($id);

        echo $app->templates->render('seeds::view',
            [
                'seed' => $seed,
            ]
        );
    }

    #[Filter('LoginRequired')]
    #[Route('get', '/seeds/new')]
    public function seeds_new_get(Request $request, Application $app) {
        echo $app->templates->render('seeds::new');
    }

    #[Filter('LoginRequired')]
    #[Route('post', '/seeds/new')]
    public function seeds_new_post(Request $request, Application $app) {
        $form_vars = $request->POST;

        $seed = new Models\Seed($app->db, null);

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

        $app->templates->addData(['toast' => "Saved seed (<a href=\"/seeds/{$seed->get_id()}\">{$seed->display_string()}</a>)"]);
        echo $app->templates->render('seeds::new');
    }

    #[Filter('LoginRequired')]
    #[Route('post', '/seeds')]
    public function seeds_post(Request $request, Application $app) {
        switch ($request->POST['action']) {
            case 'delete_seed':
                $this->seeds_delete($request, $app);
                break;
        }

        $this->seeds($request, $app);
    }

    private function seeds_delete(Request $request, Application $app) {
        $seed = $app->db->seeds->find_by_id($request->POST['seed_id']);

        if (\is_null($seed)) {
            $toast_msg = "Seed does not exist with ID {$request->POST['seed_id']}";
        } else {
            $toast_msg = "Seed deleted ({$seed->common_name} - {$seed->variety})";

            try {
                $seed->delete();
            } catch (\Exception $e) {
                $toast_msg = 'Error deleting seed: '.$e;
            }
        }

        $app->templates->addData(['toast' => $toast_msg]);
    }

    #[Filter('LoginRequired')]
    #[Route('get', '/seeds/edit/{id}')]
    public function seeds_edit_get(Request $request, Application $app, string $id) {
        $seed = $app->db->seeds->find_by_id($id);

        echo $app->templates->render('seeds::edit',
            [
                'seed' => $seed,
            ]
        );
    }

    #[Filter('LoginRequired')]
    #[Route('post', '/seeds/edit/{id}')]
    public function seeds_edit_post(Request $request, Application $app, string $id) {
        $form_vars = $request->POST;
        $seed = $app->db->seeds->find_by_id($id);

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

        $app->templates->addData(['toast' => "Saved seed (<a href=\"/seeds/{$seed->get_id()}\">{$seed->display_string()}</a>)"]);
        echo $app->templates->render('seeds::view',
            [
                'seed' => $seed,
            ]
        );
    }
}
