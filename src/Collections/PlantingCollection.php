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
                        'Finished',
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
                        'Finished',
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
            foreach ($doc['transplant_log'] as $id) {
                $transplants[] = $this->db->transplants->find_by_id($id);
            }

            $parent = null;
            if ($doc['parent']) {
                $parent = $this->find_by_id($doc['parent']);
            }

            $extras = [
                'seed' => $this->db->seeds->find_by_id($doc['seed']),
                'bed' => $doc['bed'] ? $this->db->beds->find_by_id($doc['bed']) : null,
                'parent' => $parent,
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

    public function get_plantings_date(\DateTimeInterface $start_date = null, \DateTimeInterface $end_date = null, string $sort_prop = 'date', $sort_dir = -1): Models\ArrayOfPlantings
    {
        if ($start_date === null) {
            $start_date = new \DateTimeImmutable('1970-01-01');
        }

        if ($end_date === null) {
            $end_date = (new \DateTimeImmutable())->add(new \DateInterval('P1D'));
        }

        return $this->find_multiple([
            '$and' => [
                ['date' => [
                    '$gte' => $start_date->format('Y-m-d H:i:s')
                ]],
                ['date' => [
                    '$lte' => $end_date->format('Y-m-d H:i:s')
                ]],
            ]
        ], ['sort' => [$sort_prop => $sort_dir]]);
    }
}
