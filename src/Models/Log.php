<?php

declare(strict_types=1);

namespace Garden\Models;

use function Garden\BSON_array_to_array;
use MongoDB\Model\BSONDocument;

class Log extends DBRecord
{
    public \DateTimeImmutable $date;
    public ?Planting $planting = null;
    public string $planting_tag;
    public string $notes;
    public string $time_of_day;
    public array $image_files = [];
    public Weather $weather;

    public function display_string(): string
    {
        if ($this->planting) {
            return $this->planting->display_string();
        }
        return "All";
    }

    protected function load_from_record(BSONDocument $record, array $extras): void
    {
        $this->id = $record['_id'];
        $this->date = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $record['date']);
        $this->notes = $record['notes'];
        $this->planting_tag = $record['planting_tag'] ?? '';
        $this->time_of_day = $record['time_of_day'];
        $this->weather = $extras['weather'];

        if ($record->offsetExists('image_files')) {
            $this->image_files = BSON_array_to_array($record['image_files']);
        }

        if ($record['planting']) {
            $this->planting = $extras['planting'];
        }
    }

    public function to_array(): array
    {
        return [
            'date' => $this->date->format('Y-m-d H:i:s'),
            'planting' => is_null($this->planting) ? null : $this->planting->get_id_obj(),
            'notes' => $this->notes,
            'time_of_day' => $this->time_of_day,
            'image_files' => $this->image_files,
            'planting_tag' => $this->planting_tag,
        ];
    }
}
