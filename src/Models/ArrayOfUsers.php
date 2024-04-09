<?php

declare(strict_types=1);

namespace Garden\Models;

class ArrayOfUsers extends \ArrayObject
{
    public function offsetSet($key, $val): void
    {
        if ($val instanceof User) {
            parent::offsetSet($key, $val);
            return;
        }
        throw new \InvalidArgumentException('Value must be a User, ' . get_class($val) . ' given.');
    }
}
