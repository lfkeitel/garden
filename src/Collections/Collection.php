<?php

declare(strict_types=1);

namespace Garden\Collections;

use Garden\DatabaseConnection;
use Garden\Models\DBRecord;
use MongoDB;
use MongoDB\BSON\ObjectId;

use function Garden\get_class_name;

abstract class Collection
{
    protected DatabaseConnection $db;
    protected string $collection;
    protected string $default_sort_prop = 'date';

    public function __construct(DatabaseConnection $db)
    {
        $this->db = $db;

        $name = get_class_name($this);
        $name = \str_replace('Collection', '', $name);
        $this->collection = strtolower($name);
    }

    public function create(DBRecord $record): MongoDB\InsertOneResult
    {
        $collection = $this->collection;
        $res = $this->db->get_mongodb_collection($collection)->insertOne($record->to_array());
        $record->set_id($res->getInsertedId());
        return $res;
    }

    public function save(DBRecord $record): MongoDB\UpdateResult
    {
        $collection = $this->collection;
        return $this->db->get_mongodb_collection($collection)->replaceOne(
            ['_id' => $record->get_id_obj()],
            $record->to_array()
        );
    }

    public function delete(DBRecord $record): MongoDB\DeleteResult
    {
        $collection = $this->collection;
        return $this->db->get_mongodb_collection($collection)->deleteOne(
            ['_id' => $record->get_id_obj()]
        );
    }

    public function aggregate(array $pipeline): array
    {
        $collection = $this->db->get_mongodb_collection($this->collection);
        return $collection->aggregate($pipeline)->toArray();
    }

    public function get_all(?string $sort_prop = null, $sort_dir = 1, array $filter = []): \ArrayObject
    {
        $sort_prop = \is_null($sort_prop) ? $this->default_sort_prop : $sort_prop;
        return $this->find_multiple($filter, ['sort' => [$sort_prop => $sort_dir]]);
    }

    public function find_by_id(string|ObjectID $id): mixed
    {
        $id = $id instanceof ObjectId ? $id : new ObjectId($id);
        return $this->find_one('_id', $id);
    }

    public function find_one(string $prop, mixed $val): mixed
    {
        $records = $this->find_multiple([$prop => $val]);

        if (\count($records) > 0) {
            return $records[0];
        }

        return null;
    }

    public function find_multiple(array $filter = [], array $options = []): \ArrayObject
    {
        $collection = $this->db->get_mongodb_collection($this->collection);
        $all_items = $collection->find($filter, $options);
        return $this->results_to_result_set($all_items);
    }

    abstract protected function results_to_result_set($records): \ArrayObject;
}
