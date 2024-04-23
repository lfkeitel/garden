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
namespace Onesimus\Router\Exceptions;

class FailedFilterException extends \Exception
{
    public string $filter_name;
    public mixed $response;

    public function __construct(string $filter_name, mixed $filter_resp)
    {
        parent::__construct("Filter '{$filter_name}' failed");

        $this->filter_name = $filter_name;
        $this->response = $response;
    }
}
