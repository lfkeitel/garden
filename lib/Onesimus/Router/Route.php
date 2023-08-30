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
namespace Onesimus\Router;

class Route
{
    // HTTP method this route responds to
    // Typically set to GET, POST, or ANY
    protected string $httpmethod;

    // URI pattern this route matches
    protected string $pattern;

    // Closure assigned to this route if given
    protected \Closure $callable;

    // Class to spawn upon dispatch
    protected string $class;

    // Class method to call upon dispatch
    protected string $method;

    // Filters to apply, applied in order of assignment
    protected array $filters;

    /**
     * Create a new route
     *
     * @param string $httpmethod Method this route responds to
     * @param string $pattern    URI pattern this route matches
     * @param string/Closure $callback Class@method or closure to call on dispatch
     * @param array/string $options
     *        If a string, $options is a single filter
     *        If an array, the key 'filter' is an array of filter(s) to run before dispatch
     */
    public function __construct(string $httpmethod, string $pattern, \Closure|string $callback, array $options = ['filter' => []])
    {
        if (!$options) {
            $options = ['filter' => []];
        }

        if (!is_array($options)) {
            $options = ['filter' => [$options]];
        }

        if (!is_array($options['filter'])) {
            $options['filter'] = [$options['filter']];
        }

        if ($callback instanceof \Closure) {
            $this->callable = $callback;
        } else {
            list($class, $method) = explode('@', $callback, 2);
            $this->class = $class;
            $this->method = $method;
        }

        $this->httpmethod = $httpmethod;
        $this->pattern = $pattern;
        $this->filters = $options['filter'];
    }

    public function getHttpmethod(): string {
        return $this->httpmethod;
    }

    public function getPattern(): string {
        return $this->pattern;
    }

    public function getCallable(): \Closure {
        return $this->callable;
    }

    public function getClass(): string {
        return $this->class;
    }

    public function getMethod(): string {
        return $this->method;
    }

    public function getFilters(): array {
        return $this->filters;
    }

    /**
     * Return T/F if the route has a Closure for its dispatch
     * @return boolean
     */
    public function hasClosure(): bool
    {
        return is_null($this->callable);
    }

    /**
     * Determines T/F if this route matches the given $url and $method
     *
     * @param  string $url    Url to attempt match
     * @param  string $method HTTP method to match
     * @return bool
     */
    public function matches($url, $method): bool
    {
        if ($this->httpmethod !== 'ANY' && strtolower($method) !== strtolower($this->httpmethod)) {
            return false;
        }

        // Exact match OR pattern match
        return $this->pattern == $url || $this->patternMatch($url);
    }

    /**
     * Determines if this route matches a variable path
     *
     * @param  string $url Url to attemt match
     * @return bool
     */
    private function patternMatch($url): bool
    {
        $url = explode('/', $url);
        $pattern = explode('/', $this->pattern);

        $lastElement = $pattern[count($pattern) - 1];
        $isCatchAll = ($lastElement === '*' || substr($lastElement, 0, 2) === '{*');

        if (($isCatchAll && count($url) < count($pattern) -1) || (!$isCatchAll && count($url) > count($pattern))) {
            return false;
        }

        foreach ($pattern as $pkey => $pvalue) {
            if (!isset($url[$pkey])) {
                if (substr($pvalue, 0, 2) === '{?') {
                    // Piece not in URL but is optional
                    continue;
                } else {
                    // Piece not in URL and is required
                    return $isCatchAll;
                }
            }

            if ($pvalue != $url[$pkey] && substr($pvalue, 0, 1) != '{') {
                // Doesn't contain a required part
                return false;
            }
        }

        return true;
    }

    /**
     * Assigns a score to this route based on the $url
     *
     * This function is used to decide between possibly ambiguous routes if using only
     * the matches() method. For example, for the route /home/dash/char, the route
     * /home/dash/{?area} will be given a higher score than /home/{page}/{?area} even
     * though they both match. Matching static parts are given 2 points, matching variable
     * parts are given one point. As such, the first route would score 7 points while the other
     * would score 6 points. Thus the first route matches better and will take precedence.
     *
     * @param  string $url    Url to score
     * @param  string $method HTTP method of request
     * @return integer        Strength of the match
     */
    public function getScore(string $url, string $method): int
    {
        if ($this->httpmethod !== 'ANY' && strtolower($method) !== strtolower($this->httpmethod)) {
            return 0;
        }

        $url = explode('/', $url);
        $pattern = explode('/', $this->pattern);
        $score = 0;

        $lastElement = $pattern[count($pattern) - 1];
        $isCatchAll = ($lastElement === '*' || substr($lastElement, 0, 2) === '{*');

        if (($isCatchAll && count($url) < count($pattern) -1) || (!$isCatchAll && count($url) > count($pattern))) {
            return 0;
        }

        foreach ($pattern as $index => $value) {
            $isVar = substr($value, 0, 1) === '{';
            $isOpt = substr($value, 0, 2) === '{?';
            $isRest = substr($value, 0, 2) === '{*';

            if ($isRest) {
                $score += 100;
                // break;
            }

            if (!isset($url[$index])) {
                if ($isOpt) {
                    // Piece not in URL but is optional
                    continue;
                } else {
                    // Piece not in URL and is required
                    return 0;
                }
            }

            if ($value != $url[$index] && !$isVar) {
                return 0; // Doesn't contain a required part
            } elseif ($value == $url[$index] && !$isVar) {
                $score += 2; // Matching static part
            } elseif ($isVar || $isOpt) {
                $score++; // Has variable part
            }
        }

        return $score;
    }

    /**
     * Extracts variable values from the given $url based on the route pattern
     *
     * @param  string $url URL to use for extraction
     * @return array       Values from URL in order from left to right
     */
    public function getVars(string $url): array
    {
        $pattern = explode('/', $this->pattern);
        $url = explode('/', $url);
        $vars = [];
        foreach ($pattern as $key => $value) {
            if (substr($value, 0, 2) === '{*') {
                $vars []= implode('/', array_splice($url, $key));
                break;
            }

            if (substr($value, 0, 1) === '{' || substr($value, 0, 2) === '{?') {
                $vars []= isset($url[$key]) ? $url[$key] : null;
            }
        }
        return $vars;
    }
}
