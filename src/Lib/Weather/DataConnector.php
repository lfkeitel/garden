<?php

declare(strict_types=1);

namespace Garden\Lib\Weather;

use Garden\Models\DBRecord;
use Garden\Models\Weather;
use MongoDB;

interface DataConnector
{
    public function get_by_date(string $date): ?Weather;
    public function create(DBRecord $record): MongoDB\InsertOneResult;
}
