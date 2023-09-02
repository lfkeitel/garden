<?php
declare(strict_types=1);
namespace Garden\Collections;

use Garden\Models;
use Garden\Lib\WeatherDataConnector;
use MongoDB\BSON\ObjectId;

class WeatherCollection extends Collection implements WeatherDataConnector {
    // $date = 'yyyy-mm-dd'
    public function get_by_date(string $date): ?Models\Weather {
        return $this->find_one('date', $date);
    }

    public function find_one(string $prop, mixed $val): ?Models\Weather {
        $records = $this->find_multiple([$prop => $val]);

        if (\count($records) > 0) {
            return $records[0];
        }

        return null;
    }

    public function find_multiple(array $filter = [], array $options = []): Models\ArrayOfWeather {
        $collection = $this->db->get_mongodb_collection($this->collection);
        $all_items = $collection->find($filter, $options);
        $records = new Models\ArrayOfWeather();
        foreach ($all_items as $record) {
            $records []= new Models\Weather($record);
        }
        return $records;
    }
}
