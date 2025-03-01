<?php

declare(strict_types=1);

namespace Garden\Models;

use Garden\Application;
use MongoDB\Model\BSONDocument;

class Bed extends DBRecord
{
    public \DateTimeImmutable $added;
    public string $name;
    public ?Garden $garden = null;
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
        $this->garden = $extras['garden'];
    }

    public function get_plantings(Application $app): ArrayOfPlantings
    {
        return $app->db->plantings->get_in_bed($this->get_id_obj());
    }

    public function display_string_garden(): string
    {
        if ($this->garden) {
            return  "{$this->garden->display_string()}";
        }
        return 'N/A';
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
            'garden' => $this->garden->get_id_obj(),
        ];
    }

    public function display_string(): string
    {
        return $this->name;
    }
}