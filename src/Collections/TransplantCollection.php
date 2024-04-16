<?php

declare(strict_types=1);

namespace Garden\Collections;

use Garden\Models;
use MongoDB\BSON\ObjectId;

class TransplantCollection extends Collection
{
    protected function results_to_result_set($all_items): \ArrayObject
    {
        $records = new Models\ArrayOfTransplants();
        foreach ($all_items as $doc) {
            $extras = [
                'to_bed' => $this->db->beds->find_by_id($doc['to']['bed']),
                'from_bed' => $this->db->beds->find_by_id($doc['from']['bed']),
            ];

            $records [] = new Models\Transplant($doc, $extras);
        }
        return $records;
    }
}
