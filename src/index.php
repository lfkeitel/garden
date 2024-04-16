<?php

declare(strict_types=1);

namespace Garden;

use Onesimus\Router\Router;
use Onesimus\Router\Exceptions\FailedFilterException;

require 'include.php';

try {
    Router::dispatch($app);
} catch (FailedFilterException $e) {
    http_response_code(403);
    exit(0);
}
