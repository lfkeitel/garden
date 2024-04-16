<?php

declare(strict_types=1);

namespace Garden\Models;

class ArrayOfPlantings extends \ArrayObject
{
    public function offsetSet($key, $val): void
    {
        if ($val instanceof Planting) {
            parent::offsetSet($key, $val);
            return;
        }
        throw new \InvalidArgumentException('Value must be a Planting, '.get_class($val).' given.');
    }
}
