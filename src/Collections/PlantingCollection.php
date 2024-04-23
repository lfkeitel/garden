<?php

declare(strict_types=1);

namespace Garden\Collections;

use Garden\Models;
use MongoDB\BSON\ObjectId;

use function Garden\BSON_array_to_array;

class PlantingCollection extends Collection
{
    public function get_in_bed(string|ObjectId $id): Models\ArrayOfPlantings
    {
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

    public function get_of_seed(string|ObjectId $id): Models\ArrayOfPlantings
    {
        $id = $id instanceof ObjectId ? $id : new ObjectId($id);

        return $this->find_multiple(
            [
                'seed' => $id,
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

    protected function results_to_result_set($records): \ArrayObject
    {
        $items = new Models\ArrayOfPlantings();

        foreach ($records as $doc) {
            $transplants = new Models\ArrayOfTransplants();
            foreach ($doc['transplant_log'] ?? [] as $id) {
                $transplants[] = $this->db->transplants->find_by_id($id);
            }

            $extras = [
                'seed' => $this->db->seeds->find_by_id($doc['seed']),
                'bed' => $this->db->beds->find_by_id($doc['bed']),
                'transplant_log' => $transplants,
            ];

            $items[] = new Models\Planting($doc, $extras);
        }

        return $items;
    }

    public function get_all_tags(): array
    {
        $collection = $this->db->get_mongodb_collection($this->collection);
        return BSON_array_to_array($collection->distinct('custom_tags'));
    }
}
