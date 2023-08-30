<?php
declare(strict_types=1);
namespace Onesimus\Router\Attr;

#[\Attribute]
class Filter {
    public array $filters;

    public function __construct() {
        $this->filters = \func_get_args();
    }
}
