<?php

declare(strict_types=1);

namespace Garden\Models;

use MongoDB\Model\BSONDocument;

class Weather extends DBRecord
{
    public \DateTimeImmutable $date;
    public float $temp_high; // Celsius
    public float $temp_low; // Celsius
    public float $temp_afternoon; // Celsius
    public float $temp_night; // Celsius
    public float $temp_evening; // Celsius
    public float $temp_morning; // Celsius
    public float $precipitation; // Inches
    public float $cloud_cov; // %
    public float $humidity; // % relative

    protected function load_from_record(BSONDocument $record, array $extras): void
    {
        $this->id = $record['_id'];
        $this->date = \DateTimeImmutable::createFromFormat('Y-m-d', $record['date']);
        $this->temp_high = $record['temp_high'];
        $this->temp_low = $record['temp_low'];
        $this->temp_afternoon = $record['temp_afternoon'];
        $this->temp_night = $record['temp_night'];
        $this->temp_evening = $record['temp_evening'];
        $this->temp_morning = $record['temp_morning'];
        $this->precipitation = $record['precipitation'];
        $this->cloud_cov = $record['cloud_cov'];
        $this->humidity = $record['humidity'];
    }

    public function to_array(): array
    {
        return [
            'date' => $this->date->format('Y-m-d'),
            'temp_high' => $this->temp_high,
            'temp_low' => $this->temp_low,
            'temp_afternoon' => $this->temp_afternoon,
            'temp_night' => $this->temp_night,
            'temp_evening' => $this->temp_evening,
            'temp_morning' => $this->temp_morning,
            'precipitation' => $this->precipitation,
            'cloud_cov' => $this->cloud_cov,
            'humidity' => $this->humidity,
        ];
    }

    public function display_string(): string
    {
        return "{$this->date} - {$this->temp_high}/{$this->temp_low}&deg;C";
    }
}
