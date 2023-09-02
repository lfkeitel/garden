<?php
declare(strict_types=1);
namespace Garden\Lib;

use Garden\Models\Weather as WeatherModel;

class Weather {
    // $date = 'yyyy-mm-dd'
    public static function get_for_date(string $date): WeatherModel {
        $w = new WeatherModel();
        $w->date = \DateTimeImmutable::createFromFormat('Y-m-d', $date);
        $w->temp_high = 40.5;
        $w->temp_low = 11.5;
        $w->precipitation = 2.1;
        $w->cloud_cov = 10.0;
        return $w;
    }
}
