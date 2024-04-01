<?php
declare(strict_types=1);
namespace Garden\Models;

use MongoDB\BSON\ObjectId;
use MongoDB\Model\BSONDocument;

abstract class DBRecord {
    protected ObjectId $id; // Set by extending class

    abstract public function to_array(): array;
    abstract public function display_string(): string;
    abstract protected function load_from_record(BSONDocument $record, array $extras): void;

    public function __construct(?BSONDocument $record = null, ?array $extras = []) {
        if ($record) {
            $this->load_from_record($record, $extras);
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
