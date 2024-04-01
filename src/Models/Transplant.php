<?php
declare(strict_types=1);
namespace Garden\Models;

use MongoDB\Model\BSONDocument;

class Transplant extends DBRecord {
    public \DateTimeImmutable $date;
    public ?PlantingLocation $from = null;
    public PlantingLocation $to;
    public string $notes;

    public function display_string(): string {
        return "{$this->from->display_string()} -> {$this->to->display_string()}";
    }

    protected function load_from_record(BSONDocument $record, array $extras): void {
        $this->id = $record['_id'];
        $this->date = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $record['date']);
        $this->from = new PlantingLocation($record['from'], ['bed' => $extras['from_bed']]);
        $this->to = new PlantingLocation($record['to'], ['bed' => $extras['to_bed']]);
        $this->notes = $record['notes'];
    }

    public function to_array(): array {
        return [
            'date' => $this->date->format('Y-m-d H:i:s'),
            'from' => $this->from->to_array(),
            'to' => $this->to->to_array(),
            'notes' => $this->notes,
        ];
    }
}
