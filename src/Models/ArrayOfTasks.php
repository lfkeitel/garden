<?php

declare(strict_types=1);

namespace Garden\Models;

class ArrayOfTasks extends \ArrayObject
{
    public function offsetSet($key, $val): void
    {
        if ($val instanceof Task) {
            parent::offsetSet($key, $val);
            return;
        }
        throw new \InvalidArgumentException('Value must be a Task, '.get_class($val).' given.');
    }
}
