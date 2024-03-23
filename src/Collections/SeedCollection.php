<?php
declare(strict_types=1);
namespace Garden\Collections;

use Garden\Models;
use function Garden\BSON_array_to_array;
use MongoDB\BSON\ObjectId;

class SeedCollection extends Collection {
    public function get_all(string $sort_prop = 'common_name', $sort_dir = 1, $filter = []): Models\ArrayOfSeeds {
        return $this->find_multiple(array_merge([
            '$or' => [
                ['on_wishlist' => false],
                ['on_wishlist' => ['$exists' => false]],
            ]
        ], $filter), ['sort' => [$sort_prop => $sort_dir]]);
    }

    public function get_all_wishlist(string $sort_prop = 'common_name', $sort_dir = 1, $filter = []): Models\ArrayOfSeeds {
        return $this->find_multiple(array_merge([
            'on_wishlist' => true,
        ], $filter), ['sort' => [$sort_prop => $sort_dir]]);
    }

    public function find_by_id(string|ObjectId $id): ?Models\Seed {
        $id = $id instanceof ObjectId ? $id : new ObjectId($id);
        return $this->find_one('_id', $id);
    }

    public function find_one(string $prop, mixed $val): ?Models\Seed {
        $records = $this->find_multiple([$prop => $val]);

        if (\count($records) > 0) {
            return $records[0];
        }

        return null;
    }

    public function find_multiple(array $filter = [], array $options = []): Models\ArrayOfSeeds {
        $collection = $this->db->get_mongodb_collection($this->collection);
        $all_items = $collection->find($filter, $options);
        $records = new Models\ArrayOfSeeds();
        foreach ($all_items as $seed) {
            $records []= new Models\Seed($seed, $this->db);
        }
        return $records;
    }

    public function get_all_tags(): array {
        $collection = $this->db->get_mongodb_collection($this->collection);
        return BSON_array_to_array($collection->distinct('custom_tags'));
    }
}
