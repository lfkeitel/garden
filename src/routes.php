<?php
declare(strict_types=1);
namespace Garden;

use Onesimus\Router\Router;
use Onesimus\Router\Http\Request;

Router::registerClass('Garden\Controllers\IndexController');
Router::registerClass('Garden\Controllers\BedController');
Router::registerClass('Garden\Controllers\ImageController');
Router::registerClass('Garden\Controllers\SeedController');
Router::registerClass('Garden\Controllers\PlantingController');
Router::registerClass('Garden\Controllers\LogController');

Router::filter('LoginRequired', function (Request $request): bool {
    return $request->remote_user !== '';
});
