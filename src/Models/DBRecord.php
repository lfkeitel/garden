<?php
declare(strict_types=1);
namespace Garden\Models;

use function Garden\get_class_name;
use Garden\DatabaseConnection;
use MongoDB\BSON\ObjectId;
use MongoDB\Model\BSONDocument;
use MongoDB;

abstract class DBRecord {
    protected ObjectId $id; // Set by extending class

    abstract public function to_array(): array;
    abstract protected function load_from_record(BSONDocument $record, DatabaseConnection $db);
    abstract public function display_string(): string;

    public function __construct(?BSONDocument $record = null, ?DatabaseConnection $db = null) {
        if ($record) {
            $this->load_from_record($record, $db);
        }
    }

    final public function get_id(): string {
        return (string)$this->id;
    }

    final public function set_id(ObjectId $id): void {
        $this->id = $id;
    }

    final public function get_id_obj(): ObjectId {
        return $this->id;
    }
}
