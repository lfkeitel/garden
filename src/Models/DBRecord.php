<?php
declare(strict_types=1);
namespace Garden\Models;

use MongoDB\BSON\ObjectId;

abstract class DBRecord {
    protected ObjectId $id; // Set by extending class

    abstract public function to_array(): array;
    abstract public function display_string(): string;

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
