<?php
declare(strict_types=1);
namespace Root\Garden\Models;

use function Root\Garden\get_class_name;
use Root\Garden\DatabaseConnection;
use MongoDB\BSON\ObjectId;
use MongoDB\Model\BSONDocument;
use MongoDB;

abstract class DBRecord {
    protected DatabaseConnection $db;
    private string $collection;

    protected ObjectId $id; // Set by extending class

    abstract protected function to_array(): array;
    abstract protected function load_from_record(BSONDocument $record);
    abstract public function display_string(): string;

    public function __construct(DatabaseConnection $db, ?BSONDocument $record) {
        $this->db = $db;
        $this->collection = strtolower(get_class_name($this));

        if ($record) {
            $this->load_from_record($record);
        }
    }

    final public function get_id(): string {
        return (string)$this->id;
    }

    final public function get_id_obj(): ObjectId {
        return $this->id;
    }

    public function save(): MongoDB\UpdateResult {
        return $this->replaceRecord($this->to_array());
    }

    final protected function createRecord(array $record): MongoDB\InsertOneResult {
        $collection = $this->collection;
        $res = $this->db->get_mongodb_collection($collection)->insertOne($record);
        $this->id = $res->getInsertedId();
        return $res;
    }

    public function create(): MongoDB\InsertOneResult {
        return $this->createRecord($this->to_array());
    }

    final protected function replaceRecord(array $record): MongoDB\UpdateResult {
        $collection = $this->collection;
        return $this->db->get_mongodb_collection($collection)->replaceOne(
            ['_id' => $this->id],
            $record
        );
    }

    public function delete(): MongoDB\DeleteResult {
        $collection = $this->collection;
        return $this->db->get_mongodb_collection($collection)->deleteOne (
            ['_id' => $this->id]
        );
    }
}
