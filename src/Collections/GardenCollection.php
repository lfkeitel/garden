<?php

declare(strict_types=1);

namespace Garden\Collections;

use Garden\Models;
use MongoDB\BSON\ObjectId;

class GardenCollection extends Collection
{
    protected string $default_sort_prop = 'name';

    protected function results_to_result_set($all_items): \ArrayObject
    {
        $records = new Models\ArrayOfGardens();
        foreach ($all_items as $log) {
            $records [] = new Models\Garden($log);
        }
        return $records;
    }
}
