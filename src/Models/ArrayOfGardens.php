<?php

declare(strict_types=1);

namespace Garden\Models;

class ArrayOfGardens extends \ArrayObject
{
    public function offsetSet($key, $val): void
    {
        if ($val instanceof Garden) {
            parent::offsetSet($key, $val);
            return;
        }
        throw new \InvalidArgumentException('Value must be a Garden, '.get_class($val).' given.');
    }
}
