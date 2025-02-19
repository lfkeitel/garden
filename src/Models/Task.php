<?php

declare(strict_types=1);

namespace Garden\Models;

use MongoDB\Model\BSONDocument;

class Task extends DBRecord
{
    public \DateTimeImmutable $due;
    public string $title;
    public string $notes;
    public bool $complete;
    public ?Planting $planting;

    protected function load_from_record(BSONDocument $record, array $extras): void
    {
        $this->id = $record['_id'];
        $this->due = new \DateTimeImmutable($record['due']);
        $this->title = $record['title'];
        $this->notes = $record['notes'];
        $this->complete = $record['complete'];
        $this->planting = $extras['planting'];
    }

    public function to_array(): array
    {
        return [
            'due' => $this->due->format('Y-m-d H:i:s'),
            'title' => $this->title,
            'notes' => $this->notes,
            'complete' => $this->complete,
            'planting' => $this->planting ? $this->planting->get_id_obj() : null,
        ];
    }

    public function display_string(): string
    {
        return $this->title;
    }
}
