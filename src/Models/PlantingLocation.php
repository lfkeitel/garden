<?php

declare(strict_types=1);

namespace Garden\Models;

use MongoDB\Model\BSONDocument;

class PlantingLocation extends DBRecord
{
    public int $row;
    public int $column;
    public Bed $bed;
    public string $tray_id;

    public function display_string(): string
    {
        return "{$this->bed->name} - {$this->row}/{$this->column}";
    }

    protected function load_from_record(BSONDocument $record, array $extras): void
    {
        $this->row = $record['row'];
        $this->column = $record['column'];
        $this->tray_id = $record['tray_id'];
        $this->bed = $extras['bed'];
    }

    public function to_array(): array
    {
        return [
            'row' => $this->row,
            'column' => $this->column,
            'bed' => $this->bed->get_id_obj(),
            'tray_id' => $this->tray_id,
        ];
    }
}
