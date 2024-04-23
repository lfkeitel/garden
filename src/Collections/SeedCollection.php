<?php

declare(strict_types=1);

namespace Garden\Collections;

use Garden\Models;
use MongoDB\BSON\ObjectId;

use function Garden\BSON_array_to_array;

class SeedCollection extends Collection
{
    protected string $default_sort_prop = 'common_name';

    public function get_all(?string $sort_prop = 'common_name', $sort_dir = 1, array $filter = []): \ArrayObject
    {
        return parent::get_all($sort_prop, $sort_dir, array_merge([
            '$or' => [
                ['on_wishlist' => false],
                ['on_wishlist' => ['$exists' => false]],
            ]
        ], $filter));
    }

    public function get_all_wishlist(string $sort_prop = 'common_name', $sort_dir = 1, array $filter = []): \ArrayObject
    {
        return $this->find_multiple(array_merge([
            'on_wishlist' => true,
        ], $filter), ['sort' => [$sort_prop => $sort_dir]]);
    }

    protected function results_to_result_set($all_items): \ArrayObject
    {
        $records = new Models\ArrayOfSeeds();
        foreach ($all_items as $seed) {
            $records [] = new Models\Seed($seed);
        }
        return $records;
    }

    public function get_all_tags(): array
    {
        $collection = $this->db->get_mongodb_collection($this->collection);
        return BSON_array_to_array($collection->distinct('custom_tags'));
    }
}