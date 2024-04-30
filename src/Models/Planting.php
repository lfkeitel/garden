<?php

declare(strict_types=1);

namespace Garden\Models;

use MongoDB\Model\BSONDocument;

use function Garden\BSON_array_to_array;

class Planting extends DBRecord
{
    public int $row;
    public int $column;
    public ?Bed $bed = null;
    public Seed $seed;
    public string $status;
    public bool $is_transplant;
    public string $notes;
    public \DateTimeImmutable $date;
    public ?\DateTimeImmutable $sprout_date = null;
    public string $tray_id;
    public ?\DateTimeImmutable $harvest_date = null;
    public ArrayOfTransplants $transplant_log;
    public array $tags;
    public int $count;

    public function display_string(): string
    {
        return "{$this->seed->common_name} - {$this->seed->variety}";
    }

    protected function load_from_record(BSONDocument $record, array $extras): void
    {
        $this->id = $record['_id'];
        $this->row = $record['row'];
        $this->column = $record['column'];
        $this->status = $record['status'];
        $this->is_transplant = $record['is_transplant'];
        $this->notes = $record['notes'];
        $this->date = new \DateTimeImmutable($record['date']);
        $this->tray_id = $record['tray_id'];

        if ($record['harvest_date']) {
            $this->harvest_date = new \DateTimeImmutable($record['harvest_date']);
        }
        if ($record['sprout_date'] ?? false) {
            $this->sprout_date = new \DateTimeImmutable($record['sprout_date']);
        }

        $this->seed = $extras['seed'];
        $this->bed = $extras['bed'];
        $this->transplant_log = $extras['transplant_log'];
        $this->tags = BSON_array_to_array($record['custom_tags'] ?? []);
        $this->count = $record['count'] ?? 1;
    }

    public function to_array(): array
    {
        $transplant_ids = [];
        foreach ($this->transplant_log as $tlog) {
            \array_push($transplant_ids, $tlog->get_id_obj());
        }

        return [
            'row' => $this->row,
            'column' => $this->column,
            'bed' => is_null($this->bed) ? null : $this->bed->get_id_obj(),
            'seed' => $this->seed->get_id_obj(),
            'status' => $this->status,
            'is_transplant' => $this->is_transplant,
            'notes' => $this->notes,
            'date' => $this->date->format('Y-m-d'),
            'sprout_date' => is_null($this->sprout_date) ? null : $this->sprout_date->format('Y-m-d'),
            'tray_id' => $this->tray_id,
            'harvest_date' => is_null($this->harvest_date) ? null : $this->harvest_date->format('Y-m-d'),
            'transplant_log' => $transplant_ids,
            'custom_tags' => array_map(
                fn($value): string => \strtolower($value),
                $this->tags
            ),
            'count' => $this->count,
        ];
    }

    public function tags_to_str(): string {
        return count($this->tags) === 0 ? '' : implode(", ", $this->tags);
    }

    public function maturity_date(): string {
        if ($this->harvest_date) {
            return $this->harvest_date->format('Y-m-d');
        }

        $date = $this->date;
        if ($this->sprout_date) {
            $date = $this->sprout_date;
        }

        $day = $date->add(new \DateInterval("P{$this->seed->days_to_maturity}D"));
        return $day->format('Y-m-d');
    }

    public function germ_date(): \DateTimeInterface {
        return $this->sprout_date ? $this->sprout_date : $this->date;
    }
}