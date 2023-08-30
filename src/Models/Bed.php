<?php
declare(strict_types=1);
namespace Garden\Models;

use function Garden\BSON_array_to_array;
use Garden\DatabaseConnection;
use MongoDB\Model\BSONDocument;
use MongoDB;


class Bed extends DBRecord {
    public \DateTimeImmutable $added;
    public string $name;
    public int $rows;
    public int $cols;
    public string $notes;

    protected function load_from_record(BSONDocument $record) {
        $this->id = $record['_id'];
        $this->added = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $record['added']);
        $this->name = $record['name'];
        $this->rows = $record['rows'];
        $this->cols = $record['cols'];
        $this->notes = $record['notes'];
    }

    protected function to_array(): array {
        return [
            'added' => $this->added->format('Y-m-d H:i:s'),
            'name' => $this->name,
            'rows' => $this->rows,
            'cols' => $this->cols,
            'notes' => $this->notes,
        ];
    }

    public function display_string(): string {
        return $this->name;
    }
}
