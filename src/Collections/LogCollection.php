<?php

declare(strict_types=1);

namespace Garden\Collections;

use Garden\Models;
use Garden\Lib\Weather\Store as WeatherStore;
use MongoDB\BSON\ObjectId;

class LogCollection extends Collection
{
    public function get_planting_logs(Models\Planting $planting, string $sort_prop = 'date', $sort_dir = -1): Models\ArrayOfLogs
    {
        $harvest_date = $planting->harvest_date;
        if ($harvest_date === null) {
            $harvest_date = new \DateTimeImmutable();
        }

        return $this->find_multiple([
            '$or' => [
                ['planting' => $planting->get_id_obj()], // Logs for specific planting
                ['$and' => [ // Logs for all plantings made after planting date
                    ['planting' => null],
                    ['$or' => [
                        ['planting_tag' => ['$exists' => false]],
                        ['planting_tag' => ''],
                    ]],
                    ['date' => ['$gte' => $planting->date->format('Y-m-d H:i:s')]],
                    ['date' => ['$lte' => $harvest_date->format('Y-m-d H:i:s')]],
                ]], // Logs apploed to specific planting tags
                ['$and' => [
                    ['planting_tag' => ['$in' => $planting->tags]],
                    ['date' => ['$gte' => $planting->date->format('Y-m-d H:i:s')]],
                    ['date' => ['$lte' => $harvest_date->format('Y-m-d H:i:s')]],
                ]],
            ]
        ], ['sort' => [$sort_prop => $sort_dir]]);
    }

    public function get_logs_date(\DateTimeInterface $start_date = null, \DateTimeInterface $end_date = null, string $sort_prop = 'date', $sort_dir = -1): Models\ArrayOfLogs
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

    protected function results_to_result_set($all_items): \ArrayObject
    {
        $records = new Models\ArrayOfLogs();
        foreach ($all_items as $record) {
            $date = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $record['date']);
            $extras = [
                'weather' => WeatherStore::get_for_date($date->format('Y-m-d')),
            ];

            if ($record['planting']) {
                $extras['planting'] = $this->db->plantings->find_by_id($record['planting']);
            }

            $records[] = new Models\Log($record, $extras);
        }
        return $records;
    }
}