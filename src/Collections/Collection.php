<?php
declare(strict_types=1);
namespace Garden\Collections;

use function Garden\get_class_name;
use Garden\DatabaseConnection;

abstract class Collection {
    protected DatabaseConnection $db;
    protected string $collection;

    public function __construct(DatabaseConnection $db) {
        $this->db = $db;

        $name = get_class_name($this);
        $name = \str_replace('Collection', '', $name);
        $this->collection = strtolower($name);
    }
}
