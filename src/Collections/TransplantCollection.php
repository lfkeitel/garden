<?php
declare(strict_types=1);
namespace Garden\Collections;

use Garden\Models;
use MongoDB\BSON\ObjectId;

class TransplantCollection extends Collection {
    public function get_all(array $filter = [], string $sort_prop = 'date', $sort_dir = 1): Models\ArrayOfTransplants {
        return $this->find_multiple($filter, ['sort' => [$sort_prop => $sort_dir]]);
    }

    public function find_by_id(string|ObjectID $id): ?Models\Transplant {
        $id = $id instanceof ObjectId ? $id : new ObjectId($id);
        return $this->find_one('_id', $id);
    }

    public function find_one(string $prop, mixed $val): ?Models\Transplant {
        $records = $this->find_multiple([$prop => $val]);

        if (\count($records) > 0) {
            return $records[0];
        }

        return null;
    }

    public function find_multiple(array $filter = [], array $options = []): Models\ArrayOfTransplants {
        $collection = $this->db->get_mongodb_collection($this->collection);
        $all_items = $collection->find($filter, $options);
        $records = new Models\ArrayOfTransplants();
        foreach ($all_items as $doc) {
            $extras = [
                'to_bed' => $this->db->beds->find_by_id($doc['to']['bed']),
                'from_bed' => $this->db->beds->find_by_id($doc['from']['bed']),
            ];

            $records []= new Models\Transplant($doc, $extras);
        }
        return $records;
    }
}
