<?php

declare(strict_types=1);

namespace Garden\Collections;

use Garden\Models;
use MongoDB\BSON\ObjectId;

class UserCollection extends Collection
{
    protected string $default_sort_prop = 'name';

    protected function results_to_result_set($all_items): \ArrayObject
    {
        $records = new Models\ArrayOfUsers();
        foreach ($all_items as $log) {
            $records[] = new Models\User($log);
        }
        return $records;
    }
}
