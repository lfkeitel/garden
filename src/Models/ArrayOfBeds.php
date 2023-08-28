<?php
declare(strict_types=1);
namespace Root\Garden\Models;

class ArrayOfBeds extends \ArrayObject {
    public function offsetSet($key, $val): void {
        if ($val instanceof Bed) {
            parent::offsetSet($key, $val);
            return;
        }
        throw new \InvalidArgumentException('Value must be a Bed, '.get_class($val).' given.');
    }
}
