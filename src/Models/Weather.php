<?php
declare(strict_types=1);
namespace Garden\Models;

use MongoDB\Model\BSONDocument;


class Weather extends DBRecord {
    public \DateTimeImmutable $date;
    public float $temp_high; // Celsius
    public float $temp_low; // Celsius
    public float $precipitation; // Inches
    public float $cloud_cov; // %

    public function __construct(?BSONDocument $record = null) {
        if ($record) {
            $this->load_from_record($record);
        }
    }

    protected function load_from_record(BSONDocument $record) {
        $this->id = $record['_id'];
        $this->date = \DateTimeImmutable::createFromFormat('Y-m-d', $record['date']);
        $this->temp_high = $record['temp_high'];
        $this->temp_low = $record['temp_low'];
        $this->precipitation = $record['precipitation'];
        $this->cloud_cov = $record['cloud_cov'];
    }

    public function to_array(): array {
        return [
            'date' => $this->date->format('Y-m-d H:i:s'),
            'temp_high' => $this->temp_high,
            'temp_low' => $this->temp_low,
            'precipitation' => $this->precipitation,
            'cloud_cov' => $this->cloud_cov,
        ];
    }

    public function display_string(): string {
        return "{$this->date} - {$this->temp_high}/{$this->temp_low}";
    }
}
