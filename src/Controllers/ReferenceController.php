<?php

declare(strict_types=1);

namespace Garden\Controllers;

use Garden\Application;
use Onesimus\Router\Http\Request;
use Onesimus\Router\Attr\Route;
use Onesimus\Router\Attr\Route404;

class ReferenceController
{
    #[Route('get', '/reference')]
    public function index(Request $request, Application $app)
    {
        $this->page($request, $app, 'index');
    }

    #[Route('get', '/reference/{page}')]
    public function page(Request $request, Application $app, string $page)
    {
        echo $app->templates->render(
            "reference::{$page}"
        );
    }
}
