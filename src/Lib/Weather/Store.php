<?php

declare(strict_types=1);

namespace Garden\Lib\Weather;

use Garden\Models\Weather as WeatherModel;

class Store
{
    public static DataConnector $db;
    public static string $address = 'https://api.openweathermap.org/data/3.0/onecall/day_summary';
    public static string $apikey = '';
    public static array $location = [
        'lat' => '',
        'lon' => '',
    ];

    private static array $cache = [];

    // $date = 'yyyy-mm-dd'
    public static function get_for_date(string $date): WeatherModel
    {
        if (array_key_exists($date, self::$cache)) {
            return self::$cache[$date];
        }

        $dbData = self::$db->get_by_date($date);
        if (!is_null($dbData)) {
            return $dbData;
        }

        $data = self::get_openweather_data($date);

        $w = new WeatherModel();
        $w->date = \DateTimeImmutable::createFromFormat('Y-m-d', $date);

        if (is_null($data)) {
            return $w;
        }

        $w->temp_high = $data['temperature']['max'];
        $w->temp_low = $data['temperature']['min'];
        $w->temp_afternoon = $data['temperature']['afternoon'];
        $w->temp_night = $data['temperature']['night'];
        $w->temp_evening = $data['temperature']['evening'];
        $w->temp_morning = $data['temperature']['morning'];
        $w->precipitation = $data['precipitation']['total'];
        $w->cloud_cov = $data['cloud_cover']['afternoon'];
        $w->humidity = $data['humidity']['afternoon'];

        self::$db->create($w);
        return $w;
    }

    public static function get_openweather_data(string $date): ?array
    {
        if (self::$apikey === '') {
            return null;
        }

        $address = self::$address;
        $lat = self::$location['lat'];
        $lon = self::$location['lon'];
        $apikey = self::$apikey;

        $url = "{$address}?lat={$lat}&lon={$lon}&date={$date}&units=metric&appid={$apikey}";
        $json = \file_get_contents($url);
        if ($json === false) {
            return null;
        }

        return \json_decode($json, true);
    }
}
