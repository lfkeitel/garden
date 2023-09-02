<?php
declare(strict_types=1);
namespace Garden\Models;

class ArrayOfWeather extends \ArrayObject {
    public function offsetSet($key, $val): void {
        if ($val instanceof Weather) {
            parent::offsetSet($key, $val);
            return;
        }
        throw new \InvalidArgumentException('Value must be a Weather, '.get_class($val).' given.');
    }
}
