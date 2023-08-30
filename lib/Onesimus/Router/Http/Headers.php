<?php
declare(strict_types=1);
/**
 * OSRouter is a simple HTTP router for PHP.
 *
 * @author Lee Keitel <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license BSD 3-Clause
 */
namespace Onesimus\Router\Http;

class Headers implements \IteratorAggregate
{
    protected $headers = [];

    public function __construct(array $headers = [])
    {
        $this->replace($headers);
    }

    public function replace(array $data)
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function get(string $header)
    {
        return $this->headers[static::normalizeKey($header)];
    }

    public function set(string $header, string $data)
    {
        $this->headers[static::normalizeKey($header)] = $data;
    }

    public function __get(string $header): mixed
    {
        return $this->get($header);
    }

    public function __set(string $header, mixed $data): void
    {
        $this->set($header, $data);
    }

    public function __isset(string $header)
    {
        return isset($this->headers[static::normalizeKey($header)]);
    }

    public function __unset(string $header)
    {
        $this->remove($header);
    }

    public function remove(string $header)
    {
        unset($this->headers[static::normalizeKey($header)]);
    }

    /**
     * Transform header name into canonical form
     * @param  string $key
     * @return string
     */
    protected static function normalizeKey(string $key): string
    {
        $key = strtolower($key);
        $key = str_replace(array('-', '_'), ' ', $key);
        $key = preg_replace('#^http #', '', $key);
        $key = ucwords($key);
        $key = str_replace(' ', '-', $key);

        return $key;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->headers);
    }

    public static function fromEnvironment(): Headers
    {
        $headers = [];

        foreach($_SERVER as $key => $value) {
            if (substr($key, 0, 5) <> 'HTTP_') {
                continue;
            }

            $header = static::normalizeKey(substr($key, 5));
            $headers[$header] = $value;
        }

        return new self($headers);
    }
}
