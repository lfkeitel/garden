<?php

declare(strict_types=1);

namespace Garden;

function BSON_array_to_array(iterable $a): array
{
    $r = [];
    foreach ($a as $i) {
        if (!empty($i)) {
            $r [] = $i;
        }
    }
    return $r;
}

function buffer_var_dump($var): string
{
    ob_clean();
    ob_start();
    var_dump($var);
    $r = ob_get_contents();
    ob_end_clean();
    return $r;
}

function get_class_name(mixed $object): string
{
    $classname = \get_class($object);
    if ($pos = strrpos($classname, '\\')) {
        return substr($classname, $pos + 1);
    }
    return $pos;
}

function is_web_request(): bool
{
    return \array_key_exists('REQUEST_URI', $_SERVER);
}

function next_day(int $month, int $day)
{
    $this_year = \intval(\date('Y'));
    $this_month = \intval(\date('m'));
    $this_day = \intval(\date('d'));

    if (($this_month === $month && $this_day > $day) || $this_month > $month) {
        $this_year++;
    }

    return \DateTimeImmutable::createFromFormat('Y-m-d', "{$this_year}-{$month}-{$day}");
}
