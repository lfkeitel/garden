<?php
declare(strict_types=1);
namespace Onesimus\Router\Attr;

#[\Attribute]
class Route {
    public string $httpmethod;
    public string $pattern;

    public function __construct(string $method, string $pattern)
    {
        $this->method = strtoupper($method);
        $this->pattern = $pattern;
    }
}
