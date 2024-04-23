<?php

declare(strict_types=1);

namespace Garden\Controllers;

use Garden\Models;
use Garden\Application;
use Garden\Lib\LoginRequired;
use Onesimus\Router\Http\Request;
use Onesimus\Router\Attr\Route;
use Onesimus\Router\Attr\Filter;

class SeedController
{
    #[Route('get', '/seeds')]
    public function seeds(Request $request, Application $app)
    {
        $tag_filter = $request->GET['tag'] ?? '';
        $sort_prop = $request->GET['sort_by'] ?? 'common_name';
        $sort_dir = $request->GET['sort_dir'] ?? 1;
        $sort_dir = intval($sort_dir);
        if ($sort_dir < -1 || $sort_dir > 1) {
            $sort_dir = 1;
        }

        $filter = [];
        if ($tag_filter !== '') {
            $filter = [
                'custom_tags' => [
                    '$in' => [$tag_filter],
                ],
            ];
        }

        echo $app->templates->render(
            'seeds::index',
            [
                'allSeeds' => $app->db->seeds->get_all($sort_prop, $sort_dir, $filter),
                'allTags' => $app->db->seeds->get_all_tags(),
                'sort_by' => $sort_prop,
                'sort_dir' => $sort_dir,
            ],
        );
    }

    #[Route('get', '/wishlist')]
    public function wishlist(Request $request, Application $app)
    {
        $tag_filter = $request->GET['tag'] ?? '';
        $sort_prop = $request->GET['sort_by'] ?? 'common_name';
        $sort_dir = $request->GET['sort_dir'] ?? 1;
        $sort_dir = intval($sort_dir);
        if ($sort_dir < -1 || $sort_dir > 1) {
            $sort_dir = 1;
        }

        $filter = [];
        if ($tag_filter !== '') {
            $filter = [
                'custom_tags' => [
                    '$in' => [$tag_filter],
                ],
            ];
        }

        echo $app->templates->render(
            'seeds::index',
            [
                'allSeeds' => $app->db->seeds->get_all_wishlist($sort_prop, $sort_dir, $filter),
                'allTags' => $app->db->seeds->get_all_tags(),
                'sort_by' => $sort_prop,
                'sort_dir' => $sort_dir,
            ],
        );
    }

    #[Route('get', '/seeds/{id}')]
    public function seeds_view_get(Request $request, Application $app, string $id)
    {
        $seed = $app->db->seeds->find_by_id($id);

        echo $app->templates->render(
            'seeds::view',
            [
                'seed' => $seed,
                'avg_germ_time' => $this->seed_avg_germ_time($app, $seed),
            ]
        );
    }

    private function seed_avg_germ_time(Application $app, Models\Seed $seed): int {
        $avg_germ_time_results = $app->db->plantings->aggregate(
            [
                [
                    '$match' => [
                        'seed' => $seed->get_id_obj(),
                        'sprout_date' => ['$type' => 'string'],
                    ]
                ],
                [
                    '$group' => [
                        '_id' => 'gt', 'germ_time' => ['$avg' => ['$abs' => ['$dateDiff' => ['startDate' => ['$convert' => ['input' => ['$getField' => 'sprout_date'], 'to' => 'date']], 'endDate' => ['$convert' => ['input' => ['$getField' => 'date'], 'to' => 'date']], 'unit' => 'day']]]]
                    ]
                ]
            ],
        );

        $avg_germ_time = 0;
        if ($avg_germ_time_results) {
            $avg_germ_time = \intval($avg_germ_time_results[0]['germ_time']);
        }
        return $avg_germ_time;
    }

    #[Filter('LoginRequired')]
    #[Route('get', '/seeds/new')]
    public function seeds_new_get(Request $request, Application $app)
    {
        echo $app->templates->render('seeds::new');
    }

    #[Filter('LoginRequired')]
    #[Route('post', '/seeds/new')]
    public function seeds_new_post(Request $request, Application $app)
    {
        $form_vars = $request->POST;

        $record = new Models\Seed();

        $record->added = new \DateTimeImmutable();
        $record->type = $form_vars['seed_type'];
        $record->variety = $form_vars['variety_name'];
        $record->days_to_maturity = intval($form_vars['days_to_maturity']);
        $record->days_to_germination = intval($form_vars['days_to_germination']);
        $record->is_heirloom = $form_vars['is_heirloom'] == 'Yes';
        $record->sun = $form_vars['sun_amt'];
        $record->season = $form_vars['growing_season'] ?? ['Summer'];
        $record->characteristics = $form_vars['other_charact'] ?? [];
        $record->is_hybrid = $form_vars['is_hybrid'] == 'Yes';
        $record->source = $form_vars['source'];
        $record->link = $form_vars['source_link'];
        $record->notes = $form_vars['notes'];
        $record->on_wishlist = \array_key_exists('on_wishlist', $form_vars);
        $record->tags = [];

        $custom_tags = explode(',', $form_vars['tags']);
        foreach ($custom_tags as $tag) {
            array_push($record->tags, trim($tag));
        }

        switch ($record->type) {
            case 'Vegetable':
                $record->common_name = $form_vars['seed_vegetable_name'];
                break;
            case 'Herb':
                $record->common_name = $form_vars['seed_herb_name'];
                break;
            case 'Fruit':
                $record->common_name = $form_vars['seed_fruit_name'];
                break;
            case 'Flower':
                $record->common_name = $form_vars['seed_flower_name'];
                break;
        }

        $app->db->seeds->create($record);

        $app->templates->addData(['toast' => "Saved seed (<a href=\"/seeds/{$record->get_id()}\">{$record->display_string()}</a>)"]);
        echo $app->templates->render('seeds::new');
    }

    #[Filter('LoginRequired')]
    #[Route('post', '/seeds')]
    public function seeds_post(Request $request, Application $app)
    {
        switch ($request->POST['action']) {
            case 'delete_seed':
                $this->seeds_delete($request, $app);
                break;
        }

        $this->seeds($request, $app);
    }

    #[Filter('LoginRequired')]
    #[Route('post', '/wishlist')]
    public function seeds_post_wishlist(Request $request, Application $app)
    {
        switch ($request->POST['action']) {
            case 'delete_seed':
                $this->seeds_delete($request, $app);
                break;
        }

        $this->wishlist($request, $app);
    }

    private function seeds_delete(Request $request, Application $app)
    {
        $seed = $app->db->seeds->find_by_id($request->POST['seed_id']);

        if (\is_null($seed)) {
            $toast_msg = "Seed does not exist with ID {$request->POST['seed_id']}";
        } else {
            $toast_msg = "Seed deleted ({$seed->common_name} - {$seed->variety})";

            try {
                $app->db->seeds->delete($seed);
            } catch (\Exception $e) {
                $toast_msg = 'Error deleting seed: '.$e;
            }
        }

        $app->templates->addData(['toast' => $toast_msg]);
    }

    #[Filter('LoginRequired')]
    #[Route('get', '/seeds/edit/{id}')]
    public function seeds_edit_get(Request $request, Application $app, string $id)
    {
        $seed = $app->db->seeds->find_by_id($id);

        echo $app->templates->render(
            'seeds::edit',
            [
                'seed' => $seed,
            ]
        );
    }

    #[Filter('LoginRequired')]
    #[Route('post', '/seeds/edit/{id}')]
    public function seeds_edit_post(Request $request, Application $app, string $id)
    {
        $form_vars = $request->POST;
        $record = $app->db->seeds->find_by_id($id);

        $record->type = $form_vars['seed_type'];
        $record->variety = $form_vars['variety_name'];
        $record->days_to_maturity = intval($form_vars['days_to_maturity']);
        $record->days_to_germination = intval($form_vars['days_to_germination']);
        $record->is_heirloom = $form_vars['is_heirloom'] == 'Yes';
        $record->sun = $form_vars['sun_amt'];
        $record->season = $form_vars['growing_season'] ?? ['Summer'];
        $record->characteristics = $form_vars['other_charact'] ?? [];
        $record->is_hybrid = $form_vars['is_hybrid'] == 'Yes';
        $record->source = $form_vars['source'];
        $record->link = $form_vars['source_link'];
        $record->notes = $form_vars['notes'];
        $record->on_wishlist = \array_key_exists('on_wishlist', $form_vars);
        $record->tags = [];

        $custom_tags = explode(',', $form_vars['tags']);
        foreach ($custom_tags as $tag) {
            array_push($record->tags, trim($tag));
        }

        switch ($record->type) {
            case 'Vegetable':
                $record->common_name = $form_vars['seed_vegetable_name'];
                break;
            case 'Herb':
                $record->common_name = $form_vars['seed_herb_name'];
                break;
            case 'Fruit':
                $record->common_name = $form_vars['seed_fruit_name'];
                break;
            case 'Flower':
                $record->common_name = $form_vars['seed_flower_name'];
                break;
        }

        $app->db->seeds->save($record);

        $app->templates->addData(['toast' => "Saved seed (<a href=\"/seeds/{$record->get_id()}\">{$record->display_string()}</a>)"]);
        echo $app->templates->render(
            'seeds::view',
            [
                'seed' => $record,
                'avg_germ_time' => $this->seed_avg_germ_time($app, $record),
            ]
        );
    }
}
