<?php
declare(strict_types=1);
namespace Garden\Collections;

use Garden\Models;
use MongoDB\BSON\ObjectId;

class PlantingCollection extends Collection {
    public function get_all(array $filter = [], string $sort_prop = 'date', $sort_dir = 1): Models\ArrayOfPlantings {
        return $this->find_multiple($filter, ['sort' => [$sort_prop => $sort_dir]]);
    }

    public function find_by_id(string|ObjectID $id): ?Models\Planting {
        $id = $id instanceof ObjectId ? $id : new ObjectId($id);
        return $this->find_one('_id', $id);
    }

    public function find_one(string $prop, mixed $val): ?Models\Planting {
        $records = $this->find_multiple([$prop => $val]);

        if (\count($records) > 0) {
            return $records[0];
        }

        return null;
    }

    public function get_in_bed(string|ObjectId $id): Models\ArrayOfPlantings {
        $id = $id instanceof ObjectId ? $id : new ObjectId($id);

        return $this->find_multiple(
            [
                'bed' => $id,
                'status' => [
                    '$nin' => [
                        'Harvested',
                        'Failed',
                        'Transplanted',
                    ],
                ]
            ]
        );
    }

    public function find_multiple(array $filter = [], array $options = []): Models\ArrayOfPlantings {
        $collection = $this->db->get_mongodb_collection($this->collection);
        $all_items = $collection->find($filter, $options);
        $records = new Models\ArrayOfPlantings();
        foreach ($all_items as $doc) {
            $transplants = new Models\ArrayOfTransplants();
            foreach ($doc['transplant_log'] ?? [] as $id) {
                $transplants []= $this->db->transplants->find_by_id($id);
            }

            $extras = [
                'seed' => $this->db->seeds->find_by_id($doc['seed']),
                'bed' => $this->db->beds->find_by_id($doc['bed']),
                'transplant_log' => $transplants,
            ];

            $records []= new Models\Planting($doc, $extras);
        }
        return $records;
    }
}
