<?php
declare(strict_types=1);
namespace Garden\Lib;

use Garden\Models\DBRecord;
use Garden\Models\Weather;
use MongoDB;

interface WeatherDataConnector {
    public function get_by_date(string $date): ?Weather;
    public function create(DBRecord $record): MongoDB\InsertOneResult;
}
