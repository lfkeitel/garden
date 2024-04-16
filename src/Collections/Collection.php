<?php

declare(strict_types=1);

namespace Garden\Collections;

use Garden\DatabaseConnection;
use Garden\Models\DBRecord;
use MongoDB;

use function Garden\get_class_name;

abstract class Collection
{
    protected DatabaseConnection $db;
    protected string $collection;

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
}
