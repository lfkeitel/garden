<?php
declare(strict_types=1);
namespace Root\Garden;

use Onesimus\Router\Http\Request;
use League\Plates\Engine;

$dev_mode = false;

require '../vendor/autoload.php';
require 'config.php';

if ($dev_mode) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

require 'functions.php';
require 'database.php';
require 'routes.php';
$app_vars = require 'app_vars.php';

$request = Request::getRequest();

$db = new DatabaseConnection($mongo_db_connect);

$templates = new Engine('../templates');
$templates->addFolder('seeds', '../templates/seeds');
$templates->addFolder('plantings', '../templates/plantings');
$templates->addFolder('beds', '../templates/beds');
$templates->addFolder('logs', '../templates/logs');
$templates->addFolder('partials', '../templates/partials');

$app = new Application($db, $request, $templates);

$templates->addData([
    'app' => $app,
    'first_frost' => $app_vars['first_frost'],
    'last_frost' => $app_vars['last_frost'],
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
