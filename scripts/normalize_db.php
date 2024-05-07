<?php

declare(strict_types=1);

namespace Garden;

$cwd = dirname(__FILE__);
require "{$cwd}/../src/include.php";

function resave_collection($collection) {
    $items = $collection->get_all();

    foreach ($items as $item) {
        $collection->save($item);
    }
}

$collections = [
    'plantings',
    'beds',
    'logs',
    'seeds',
];

foreach ($collections as $collection) {
    resave_collection($db->$collection);
}
