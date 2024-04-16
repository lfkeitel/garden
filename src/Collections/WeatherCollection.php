<?php

declare(strict_types=1);

namespace Garden\Collections;

use Garden\Models;
use Garden\Lib\Weather\DataConnector as WDataConnector;
use MongoDB\BSON\ObjectId;

class WeatherCollection extends Collection implements WDataConnector
{
    // $date = 'yyyy-mm-dd'
    public function get_by_date(string $date): ?Models\Weather
    {
        return $this->find_one('date', $date);
    }

    protected function results_to_result_set($all_items): \ArrayObject
    {
        $records = new Models\ArrayOfWeather();
        foreach ($all_items as $record) {
            $records [] = new Models\Weather($record);
        }
        return $records;
    }
}
