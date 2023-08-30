<?php
declare(strict_types=1);
namespace Garden;

use Garden\DatabaseConnection;
use League\Plates\Engine;
use Onesimus\Router\Http\Request;

class Application {
    private DatabaseConnection $db;
    private Engine $templates;
    private Request $request;

    public function __construct(DatabaseConnection $db, Request $request, Engine $templates) {
        $this->db = $db;
        $this->templates = $templates;
        $this->request = $request;
    }

    public function __get(string $attr): mixed {
        return $this->$attr;
    }
}
