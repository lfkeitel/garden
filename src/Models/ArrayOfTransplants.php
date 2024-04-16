<?php

declare(strict_types=1);

namespace Garden\Models;

class ArrayOfTransplants extends \ArrayObject
{
    public function offsetSet($key, $val): void
    {
        if ($val instanceof Transplant) {
            parent::offsetSet($key, $val);
            return;
        }
        throw new \InvalidArgumentException('Value must be a Transplant, '.get_class($val).' given.');
    }
}
