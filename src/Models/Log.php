<?php
declare(strict_types=1);
namespace Garden\Models;

use function Garden\BSON_array_to_array;
use MongoDB\Model\BSONDocument;

class Log extends DBRecord {
    public \DateTimeImmutable $date;
    public ?Planting $planting = null;
    public string $notes;
    public string $time_of_day;
    public array $image_files = [];
    public Weather $weather;

    public function __construct(?BSONDocument $record = null, ?array $extras = []) {
        if ($record) {
            $this->load_from_record($record, $extras);
        }
    }

    public function display_string(): string {
        if ($this->planting) {
            return $this->planting->display_string();
        }
        return "All";
    }

    protected function load_from_record(BSONDocument $record, array $extras) {
        $this->id = $record['_id'];
        $this->date = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $record['date']);
        $this->notes = $record['notes'];
        $this->time_of_day = $record['time_of_day'];
        $this->weather = $extras['weather'];

        if ($record->offsetExists('image_files')) {
            $this->image_files = BSON_array_to_array($record['image_files']);
        }

        if($record['planting']) {
            $this->planting = $extras['planting'];
        }
    }

    public function to_array(): array {
        return [
            'date' => $this->date->format('Y-m-d H:i:s'),
            'planting' => is_null($this->planting) ? null : $this->planting->get_id_obj(),
            'notes' => $this->notes,
            'time_of_day' => $this->time_of_day,
            'image_files' => $this->image_files,
        ];
    }
}
