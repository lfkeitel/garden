<?php
declare(strict_types=1);
namespace Root\Garden\Models;

class ArrayOfLogs extends \ArrayObject {
    public function offsetSet($key, $val): void {
        if ($val instanceof Log) {
            parent::offsetSet($key, $val);
            return;
        }
        throw new \InvalidArgumentException('Value must be a Log, '.get_class($val).' given.');
    }
}
