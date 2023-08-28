<?php
declare(strict_types=1);
namespace Root\Garden\Collections;

use Root\Garden\Models;
use MongoDB\BSON\ObjectId;

class LogCollection extends Collection {
    public function get_all(string $sort_prop = 'date', $sort_dir = -1): Models\ArrayOfLogs {
        return $this->find_multiple([], ['sort' => [$sort_prop => $sort_dir]]);
    }

    public function get_planting_logs(string|ObjectId $id, string $sort_prop = 'date', $sort_dir = -1): Models\ArrayOfLogs {
        $id = $id instanceof ObjectId ? $id : new ObjectId($id);
        return $this->find_multiple([
            '$or' => [
                ['planting' => $id],
                ['planting' => null]
            ]
        ], ['sort' => [$sort_prop => $sort_dir]]);
    }

    public function find_by_id(string|ObjectId $id): ?Models\Log {
        $id = $id instanceof ObjectId ? $id : new ObjectId($id);
        return $this->find_one('_id', $id);
    }

    public function find_one(string $prop, mixed $val): ?Models\Log {
        $records = $this->find_multiple([$prop => $val]);

        if (\count($records) > 0) {
            return $records[0];
        }

        return null;
    }

    public function find_multiple(array $filter = [], array $options = []): Models\ArrayOfLogs {
        $collection = $this->db->get_mongodb_collection($this->collection);
        $all_items = $collection->find($filter, $options);
        $records = new Models\ArrayOfLogs();
        foreach ($all_items as $log) {
            $records []= new Models\Log($this->db, $log);
        }
        return $records;
    }
}
