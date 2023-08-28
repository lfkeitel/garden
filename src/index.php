<?php
declare(strict_types=1);
namespace Root\Garden;

use Onesimus\Router\Router;

require 'include.php';

$route = Router::route($request);
$route->dispatch($app);
