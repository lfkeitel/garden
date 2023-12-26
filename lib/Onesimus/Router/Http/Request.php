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

class Request
{
    const METHOD_HEAD = 'HEAD';
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_PATCH = 'PATCH';
    const METHOD_DELETE = 'DELETE';
    const METHOD_OPTIONS = 'OPTIONS';

    private static ?Request $instance = null;

    private array $properties;

    public Headers $headers;

    public array $context = []; // Used for arbitrary data while handling the request.

    public static function getRequest(string $basepath = ''): Request
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();

            $path = (self::$instance)->REQUEST_URI;
            if ($basepath !== '' && \str_starts_with($path, $basepath)) {
                (self::$instance)->REQUEST_URI = \substr($path, \strlen($basepath));
            }
        }
        return self::$instance;
    }

    public static function mock(array $settings = []): Request
    {
        $defaults = [
            'SERVER_ADDR' => '127.0.0.1',
            'REQUEST_METHOD' => 'GET',
            'SCRIPT_FILENAME' => '',
            'QUERY_STRING' => '',
            'SERVER_NAME' => 'localhost',
            'SERVER_PORT' => 80,
            'USER_AGENT' => '',
            'REMOTE_ADDR' => '127.0.0.1',
            'URL_SCHEME' => 'http',
            'REQUEST_URI' => '',
        ];
        self::$instance = new self(array_merge($defaults, $settings));

        return self::$instance;
    }

    private function __construct(?array $settings = null)
    {
        if ($settings) {
            $this->properties = $settings;
        } else {
            $env = [];

            $env['SERVER_ADDR'] = $this->hasProperty($_SERVER, 'SERVER_ADDR');
            $env['SERVER_NAME'] = $this->hasProperty($_SERVER, 'SERVER_NAME');
            $env['SERVER_PORT'] = $this->hasProperty($_SERVER, 'SERVER_PORT');
            $env['REMOTE_USER'] = $this->hasProperty($_SERVER, 'REMOTE_USER');
            $env['REMOTE_ADDR'] = $this->hasProperty($_SERVER, 'REMOTE_ADDR');
            $env['REQUEST_METHOD'] = $this->hasProperty($_SERVER, 'REQUEST_METHOD');
            $env['SCRIPT_FILENAME'] = $this->hasProperty($_SERVER, 'SCRIPT_FILENAME');
            $env['REQUEST_URI'] = explode('?', $_SERVER['REQUEST_URI'])[0];
            $env['URL_SCHEME'] = (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off') ? 'http' : 'https';
            $env['QUERY_STRING'] = $this->hasProperty($_SERVER, 'QUERY_STRING');

            $rawInput = @file_get_contents('php://input');
            if (!$rawInput) {
                $rawInput = '';
            }
            $env['INPUT'] = $rawInput;
            $env['POST'] = $_POST;
            $env['GET'] = $_GET;

            $this->properties = $env;
            $this->headers = Headers::fromEnvironment();
        }
    }

    private function hasProperty(array $server, string $var, mixed $default = '')
    {
        return \array_key_exists($var, $server) ? $server[$var] : $default;
    }

    public function __get(string $key): mixed
    {
        return $this->getProp(\strtoupper($key));
    }

    public function getProp(string $key): mixed
    {
        return $this->properties[$key];
    }

    public function __set(string $key, mixed $name): void
    {
        $this->setProp(\strtoupper($key), $name);
    }

    public function setProp(string $key, mixed $name): void
    {
        $this->properties[$key] = $name;
    }

    public function postParam($key = null, $default = null): string
    {
        if ($key) {
            if (array_key_exists($key, $this->properties['POST'])) {
                return urldecode($this->properties['POST'][$key]);
            } else {
                return $default;
            }
        } else {
            return $this->properties['POST'];
        }
    }

    public function getParam($key = null, $default = null): string
    {
        if ($key) {
            if (array_key_exists($key, $this->properties['GET'])) {
                return urldecode($this->properties['GET'][$key]);
            } else {
                return $default;
            }
        } else {
            return $this->properties['GET'];
        }
    }

    public function getMethod(): string
    {
        return $this->properties['REQUEST_METHOD'];
    }

    /**
     * Is this a GET request?
     * @return bool
     */
    public function isGet(): bool
    {
        return $this->getMethod() === self::METHOD_GET;
    }

    /**
     * Is this a POST request?
     * @return bool
     */
    public function isPost(): bool
    {
        return $this->getMethod() === self::METHOD_POST;
    }

    /**
     * Is this a PUT request?
     * @return bool
     */
    public function isPut(): bool
    {
        return $this->getMethod() === self::METHOD_PUT;
    }

    /**
     * Is this a PATCH request?
     * @return bool
     */
    public function isPatch(): bool
    {
        return $this->getMethod() === self::METHOD_PATCH;
    }

    /**
     * Is this a DELETE request?
     * @return bool
     */
    public function isDelete(): bool
    {
        return $this->getMethod() === self::METHOD_DELETE;
    }

    /**
     * Is this a HEAD request?
     * @return bool
     */
    public function isHead(): bool
    {
        return $this->getMethod() === self::METHOD_HEAD;
    }

    /**
     * Is this a OPTIONS request?
     * @return bool
     */
    public function isOptions(): bool
    {
        return $this->getMethod() === self::METHOD_OPTIONS;
    }
}
