<?php

declare(strict_types=1);

namespace Garden\Collections;

use Garden\Models;
use MongoDB\BSON\ObjectId;

class BedCollection extends Collection
{
    protected string $default_sort_prop = 'name';

    public function get_in_garden(string|ObjectId $id): Models\ArrayOfBeds
    {
        $id = $id instanceof ObjectId ? $id : new ObjectId($id);

        return $this->find_multiple(
            [
                'garden' => $id,
                'hide_from_home' => false,
            ],
            ['sort' => ['name' => 1]]
        );
    }

    protected function results_to_result_set($all_items): \ArrayObject
    {
        $records = new Models\ArrayOfBeds();
        foreach ($all_items as $doc) {
            $extras = [
                'garden' => $doc->offsetExists('garden') ? $this->db->gardens->find_by_id($doc['garden']) : null,
            ];

            $records [] = new Models\Bed($doc, $extras);
        }
        return $records;
    }
}