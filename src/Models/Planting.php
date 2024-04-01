<?php
declare(strict_types=1);
namespace Garden\Models;

use MongoDB\Model\BSONDocument;

class Planting extends DBRecord {
    public int $row;
    public int $column;
    public Bed $bed;
    public Seed $seed;
    public string $status;
    public bool $is_transplant;
    public string $notes;
    public \DateTimeImmutable $date;
    public string $tray_id;
    public ?\DateTimeImmutable $harvest_date = null;
    public ArrayOfTransplants $transplant_log;

    public function display_string(): string {
        return "{$this->seed->common_name} - {$this->seed->variety}";
    }

    protected function load_from_record(BSONDocument $record, array $extras): void {
        $this->id = $record['_id'];
        $this->row = $record['row'];
        $this->column = $record['column'];
        $this->status = $record['status'];
        $this->is_transplant = $record['is_transplant'];
        $this->notes = $record['notes'];
        $this->date = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $record['date']);
        $this->tray_id = $record['tray_id'];

        if ($record['harvest_date']) {
            $this->harvest_date = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $record['harvest_date']);
        }

        $this->seed = $extras['seed'];
        $this->bed = $extras['bed'];
        $this->transplant_log = $extras['transplant_log'];
    }

    public function to_array(): array {
        $transplant_ids = [];
        // var_dump($this->transplant_log);
        foreach ($this->transplant_log as $tlog) {
            \array_push($transplant_ids, $tlog->get_id_obj());
        }

        return [
            'row' => $this->row,
            'column' => $this->column,
            'bed' => $this->bed->get_id_obj(),
            'seed' => $this->seed->get_id_obj(),
            'status' => $this->status,
            'is_transplant' => $this->is_transplant,
            'notes' => $this->notes,
            'date' => $this->date->format('Y-m-d H:i:s'),
            'tray_id' => $this->tray_id,
            'harvest_date' => is_null($this->harvest_date) ? null : $this->harvest_date->format('Y-m-d H:i:s'),
            'transplant_log' => $transplant_ids,
        ];
    }
}
