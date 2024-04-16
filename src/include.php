<?php

declare(strict_types=1);

namespace Garden;

use Onesimus\Router\Http\Request;
use League\Plates\Engine;
use Garden\Models\Planting;

$cwd = dirname(__FILE__);
require "{$cwd}/../vendor/autoload.php";

require 'functions.php';

$config = [
    'dev_mode' => false,
    'session_timeout' => 3600,
];
require 'config.php';

if ($config['dev_mode']) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

if (is_web_request()) {
    \ini_set("session.gc_maxlifetime", $config['session_timeout']);
    \session_save_path(__DIR__ . '/../sessions');
    \session_start();

    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $config['session_timeout'])) {
        session_unset();
        session_destroy();
    }
    $_SESSION['LAST_ACTIVITY'] = time();

    if (!isset($_SESSION['CREATED'])) {
        $_SESSION['CREATED'] = time();
    } elseif (time() - $_SESSION['CREATED'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['CREATED'] = time();
    }
}

$is_logged_in = function (): bool {
    return \array_key_exists('logged_in', $_SESSION) && $_SESSION['logged_in'] === true;
};

$basepath = isset($basepath) ? $basepath : '';

require 'database.php';
$db = new DatabaseConnection($config['mongo_db_connect']);

require 'routes.php';
$app_vars = require 'app_vars.php';

Lib\Weather\Store::$apikey = $config['openweather_apikey'];
Lib\Weather\Store::$location = $config['location'];
Lib\Weather\Store::$db = $db->weather;

$request = null;
$templates = null;

if (is_web_request()) {
    $request = Request::getRequest($basepath);
    $templates = new Engine("{$cwd}/../templates");
    $templates->addFolder('seeds', "{$cwd}/../templates/seeds");
    $templates->addFolder('plantings', "{$cwd}/../templates/plantings");
    $templates->addFolder('beds', "{$cwd}/../templates/beds");
    $templates->addFolder('logs', "{$cwd}/../templates/logs");
    $templates->addFolder('partials', "{$cwd}/../templates/partials");
    $templates->addFolder('reference', "{$cwd}/../templates/reference");

    $templates->addData([
        'basepath' => $basepath,
        'first_frost' => $app_vars['first_frost'],
        'last_frost' => $app_vars['last_frost'],
        'usda_zone' => $app_vars['usda_zone'],
        'season_length' => 365 - intval($app_vars['last_frost']->diff($app_vars['first_frost'])->format('%a')),
    ]);

    $templates->addData(
        [
            'seed_data' => $app_vars['seed_data'],
        ],
        ['seeds::new', 'seeds::edit'],
    );

    $templates->addData(
        [
            'planting_statuses' => $app_vars['planting_statuses'],
        ],
        ['plantings::new', 'plantings::edit', 'plantings::bulk_edit', 'plantings::index'],
    );

    $templates->registerFunction('days_from_date', function (\DateTimeInterface $then) {
        $diff = \date_diff($then, new \DateTimeImmutable(), true);
        return "{$diff->format('%a')} days";
    });

    $templates->registerFunction('date_plus_days', function (\DateTimeInterface $then, int $days) {
        $day = $then->add(new \DateInterval("P{$days}D"));
        return $day->format('Y-m-d');
    });

    $templates->registerFunction('plant_maturity_day', function (Planting $planting) {
        if ($planting->harvest_date) {
            return $planting->harvest_date->format('Y-m-d');
        }

        $date = $planting->date;
        if ($planting->sprout_date) {
            $date = $planting->sprout_date;
        }

        $day = $date->add(new \DateInterval("P{$planting->seed->days_to_maturity}D"));
        return $day->format('Y-m-d');
    });

    $templates->registerFunction('is_logged_in', $is_logged_in);
}

$app = new Application($db, $config, $request, $templates);

if (is_web_request()) {
    $templates->addData([
        'app' => $app,
    ]);
}
