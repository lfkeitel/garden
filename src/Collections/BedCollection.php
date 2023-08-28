<?php
declare(strict_types=1);
namespace Root\Garden\Collections;

use Root\Garden\Models;
use MongoDB\BSON\ObjectId;

class BedCollection extends Collection {
    public function get_all(string $sort_prop = 'name', $sort_dir = 1): Models\ArrayOfBeds {
        return $this->find_multiple([], ['sort' => [$sort_prop => $sort_dir]]);
    }

    public function find_by_id(string|ObjectId $id): ?Models\Bed {
        $id = $id instanceof ObjectId ? $id : new ObjectId($id);
        return $this->find_one('_id', $id);
    }

    public function find_one(string $prop, mixed $val): ?Models\Bed {
        $records = $this->find_multiple([$prop => $val]);

        if (\count($records) > 0) {
            return $records[0];
        }

        return null;
    }

    public function find_multiple(array $filter = [], array $options = []): Models\ArrayOfBeds {
        $collection = $this->db->get_mongodb_collection($this->collection);
        $all_items = $collection->find($filter, $options);
        $records = new Models\ArrayOfBeds();
        foreach ($all_items as $log) {
            $records []= new Models\Bed($this->db, $log);
        }
        return $records;
    }
}
