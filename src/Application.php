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
    private array $config;

    public function __construct(DatabaseConnection $db, array $config, Request $request, Engine $templates) {
        $this->db = $db;
        $this->templates = $templates;
        $this->request = $request;
        $this->config = $config;
    }

    public function __get(string $attr): mixed {
        return $this->$attr;
    }
}
