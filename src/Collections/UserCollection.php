<?php

declare(strict_types=1);

namespace Garden\Collections;

use Garden\Models;
use MongoDB\BSON\ObjectId;

class UserCollection extends Collection
{
    public function get_all(string $sort_prop = 'name', $sort_dir = 1): Models\ArrayOfUsers
    {
        return $this->find_multiple([], ['sort' => [$sort_prop => $sort_dir]]);
    }

    public function find_by_id(string|ObjectId $id): ?Models\User
    {
        $id = $id instanceof ObjectId ? $id : new ObjectId($id);
        return $this->find_one('_id', $id);
    }

    public function find_one(string $prop, mixed $val): ?Models\User
    {
        $records = $this->find_multiple([$prop => $val]);

        if (\count($records) > 0) {
            return $records[0];
        }

        return null;
    }

    public function find_multiple(array $filter = [], array $options = []): Models\ArrayOfUsers
    {
        $collection = $this->db->get_mongodb_collection($this->collection);
        $all_items = $collection->find($filter, $options);
        $records = new Models\ArrayOfUsers();
        foreach ($all_items as $log) {
            $records[] = new Models\User($log);
        }
        return $records;
    }
}
