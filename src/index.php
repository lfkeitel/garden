<?php

declare(strict_types=1);

namespace Garden;

use Onesimus\Router\Router;
use Onesimus\Router\Exceptions\FailedFilterException;

require 'include.php';

try {
    Router::dispatch($app);
} catch (FailedFilterException $e) {
    if ($e->filter_name === 'LoginRequired') {
        header("Location: /login", true, 307);
    } else {
        http_response_code(403);
    }
    exit(0);
}
