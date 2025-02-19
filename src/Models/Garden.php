<?php

declare(strict_types=1);

namespace Garden\Models;

use Garden\Application;
use MongoDB\Model\BSONDocument;

class Garden extends DBRecord
{
    public \DateTimeImmutable $added;
    public string $name;
    public int $rows;
    public int $cols;
    public string $notes;
    public bool $hide_from_home = false;

    protected function load_from_record(BSONDocument $record, array $extras): void
    {
        $this->id = $record['_id'];
        $this->added = new \DateTimeImmutable($record['added']);
        $this->name = $record['name'];
        $this->rows = $record['rows'];
        $this->cols = $record['cols'];
        $this->notes = $record['notes'];
        $this->hide_from_home = $record['hide_from_home'];
    }

    public function get_beds(Application $app): ArrayOfBeds
    {
        return $app->db->beds->get_in_garden($this->get_id_obj());
    }

    public function to_array(): array
    {
        return [
            'added' => $this->added->format('Y-m-d'),
            'name' => $this->name,
            'rows' => $this->rows,
            'cols' => $this->cols,
            'notes' => $this->notes,
            'hide_from_home' => $this->hide_from_home,
        ];
    }

    public function display_string(): string
    {
        return $this->name;
    }
}