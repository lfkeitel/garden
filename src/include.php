<?php
declare(strict_types=1);
namespace Garden;

use Onesimus\Router\Http\Request;
use League\Plates\Engine;

require '../vendor/autoload.php';
$config = [
    'dev_mode' => false,
    'admin_user' => [
        'username' => 'admin',
        'password' => '$2y$10$ARzGO8XROBR848CRyqhZe.9piRVhg/QsrSa6wHlTWs5jttE7KmtBC',
    ],
];
require 'config.php';

if ($config['dev_mode']) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

\session_save_path(__DIR__.'/../sessions');
\session_start();
\session_gc();

$is_logged_in = function(): bool {
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

$request = Request::getRequest($basepath);

$templates = new Engine('../templates');
$templates->addFolder('seeds', '../templates/seeds');
$templates->addFolder('plantings', '../templates/plantings');
$templates->addFolder('beds', '../templates/beds');
$templates->addFolder('logs', '../templates/logs');
$templates->addFolder('partials', '../templates/partials');

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
    ['plantings::new', 'plantings::edit', 'plantings::index'],
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
