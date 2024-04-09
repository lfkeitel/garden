<?php

declare(strict_types=1);

namespace Garden;

use Onesimus\Router\Http\Request;
use League\Plates\Engine;

require '../vendor/autoload.php';
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
} else if (time() - $_SESSION['CREATED'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['CREATED'] = time();
}

$is_logged_in = function (): bool {
    return \array_key_exists('logged_in', $_SESSION) && $_SESSION['logged_in'] === true;
};

$basepath = isset($basepath) ? $basepath : '';

require 'functions.php';
require 'database.php';
$db = new DatabaseConnection($config['mongo_db_connect']);

require 'routes.php';
$app_vars = require 'app_vars.php';

Lib\Weather\Store::$apikey = $config['openweather_apikey'];
Lib\Weather\Store::$location = $config['location'];
Lib\Weather\Store::$db = $db->weather;

$request = null;
if (\array_key_exists('REQUEST_URI', $_SERVER)) {
    $request = Request::getRequest($basepath);
}

$templates = new Engine('../templates');
$templates->addFolder('seeds', '../templates/seeds');
$templates->addFolder('plantings', '../templates/plantings');
$templates->addFolder('beds', '../templates/beds');
$templates->addFolder('logs', '../templates/logs');
$templates->addFolder('partials', '../templates/partials');
$templates->addFolder('reference', '../templates/reference');

$app = new Application($db, $config, $request, $templates);

$templates->addData([
    'app' => $app,
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

$templates->registerFunction('is_logged_in', $is_logged_in);
