<?php

declare(strict_types=1);

namespace Garden\Collections;

use Garden\Models;

class TaskCollection extends Collection
{
    protected string $default_sort_prop = 'due';

    protected function results_to_result_set($all_items): \ArrayObject
    {
        $records = new Models\ArrayOfTasks();
        foreach ($all_items as $log) {
            $planting = null;
            if ($doc['planting']) {
                $planting = $this->db->plantings->find_by_id($doc['planting']);
            }

            $extras = [
                'planting' => $planting,
            ];

            $records[] = new Models\Task($log, $extras);
        }
        return $records;
    }

    public function get_due_on(\DateTimeInterface $due_date = null, string $sort_prop = 'due', $sort_dir = -1): Models\ArrayOfTasks
    {
        if (!$due_date) {
            $due_date = new \DateTimeImmutable();
        }

        $start = new \DateTimeImmutable($due_date->format('Y-m-d'));
        $end = $start->setTime(23, 59, 59);

        return $this->find_multiple([
            '$and' => [
                ['due' => [
                    '$gte' => $start->format('Y-m-d H:i:s')
                ]],
                ['due' => [
                    '$lte' => $end->format('Y-m-d H:i:s')
                ]],
            ]
        ], ['sort' => [$sort_prop => $sort_dir]]);
    }
}
