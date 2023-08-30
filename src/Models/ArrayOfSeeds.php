<?php
declare(strict_types=1);
namespace Garden\Models;

class ArrayOfSeeds extends \ArrayObject {
    public function offsetSet($key, $val): void {
        if ($val instanceof Seed) {
            parent::offsetSet($key, $val);
            return;
        }
        throw new \InvalidArgumentException('Value must be a Seed, '.get_class($val).' given.');
    }
}
