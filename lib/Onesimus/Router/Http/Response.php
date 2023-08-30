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

class Response
{
    public Headers $headers;

    protected int $length;

    protected string $body;

    protected int $status;

    /**
     * @var array HTTP response codes and messages
     */
    protected static $messages = [
        //Informational 1xx
        100 => '100 Continue',
        101 => '101 Switching Protocols',
        //Successful 2xx
        200 => '200 OK',
        201 => '201 Created',
        202 => '202 Accepted',
        203 => '203 Non-Authoritative Information',
        204 => '204 No Content',
        205 => '205 Reset Content',
        206 => '206 Partial Content',
        //Redirection 3xx
        300 => '300 Multiple Choices',
        301 => '301 Moved Permanently',
        302 => '302 Found',
        303 => '303 See Other',
        304 => '304 Not Modified',
        305 => '305 Use Proxy',
        306 => '306 (Unused)',
        307 => '307 Temporary Redirect',
        //Client Error 4xx
        400 => '400 Bad Request',
        401 => '401 Unauthorized',
        402 => '402 Payment Required',
        403 => '403 Forbidden',
        404 => '404 Not Found',
        405 => '405 Method Not Allowed',
        406 => '406 Not Acceptable',
        407 => '407 Proxy Authentication Required',
        408 => '408 Request Timeout',
        409 => '409 Conflict',
        410 => '410 Gone',
        411 => '411 Length Required',
        412 => '412 Precondition Failed',
        413 => '413 Request Entity Too Large',
        414 => '414 Request-URI Too Long',
        415 => '415 Unsupported Media Type',
        416 => '416 Requested Range Not Satisfiable',
        417 => '417 Expectation Failed',
        418 => '418 I\'m a teapot',
        422 => '422 Unprocessable Entity',
        423 => '423 Locked',
        //Server Error 5xx
        500 => '500 Internal Server Error',
        501 => '501 Not Implemented',
        502 => '502 Bad Gateway',
        503 => '503 Service Unavailable',
        504 => '504 Gateway Timeout',
        505 => '505 HTTP Version Not Supported'
    ];

    public function __construct(string $body = '', int $status = 200, array $headers = [])
    {
        $this->setBody($body);
        $this->status = $status;
        $this->headers = new Headers(['Content-Type' => 'text/html']);
        $this->headers->replace($headers);
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body)
    {
        $this->write($body, true);
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status)
    {
        $this->status = (int) $status;
    }

    public function write(string $body, bool $replace = false)
    {
        if ($replace) {
            $this->body = $body;
        } else {
            $this->body .= (string) $body;
        }
        $this->length = strlen($this->body);
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function redirect(string $path, int $status = 302)
    {
        $this->headers->set('Location', $path);
        $this->setStatus($status);
    }

    /**
     * Finalize
     *
     * This prepares this response and returns an array
     * of [status, headers, body].
     *
     * @return array[int status, Headers headers, string body]
     */
    public function finalize(): array
    {
        // Prepare response
        if (in_array($this->status, [204, 304])) {
            $this->headers->remove('Content-Type');
            $this->headers->remove('Content-Length');
            $this->setBody('');
        }

        return [$this->status, $this->headers, $this->body];
    }

    /**
     * Get message for HTTP status code
     * @param  int         $status
     * @return string|null
     */
    public static function getMessageForCode(int $status): ?string
    {
        if (\array_key_exists($status, self::$messages)) {
            return self::$messages[$status];
        }
        return null;
    }
}
