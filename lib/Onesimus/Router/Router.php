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

class Router
{
    protected static ?Router $instance = null;
    protected static array $routes = [];
    protected static ?Route $_404route = null;

    protected static array $filters = [];

    protected function __construct() {}

    public static function getInstance(): Router
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     *  Register route for a GET request
     */
    public static function get(string $url, \Closure|string $callback, array $options = []): void
    {
        self::register('GET', $url, $callback, $options);
    }

    /**
     *  Register a route for a POST request
     */
    public static function post(string $url, \Closure|string $callback, array $options = []): void
    {
        self::register('POST', $url, $callback, $options);
    }

    /**
     *  Register a route for a PUT request
     */
    public static function put(string $url, \Closure|string $callback, array $options = []): void
    {
        self::register('PUT', $url, $callback, $options);
    }

    /**
     *  Register a route for a PATCH request
     */
    public static function patch(string $url, \Closure|string $callback, array $options = []): void
    {
        self::register('PATCH', $url, $callback, $options);
    }

    /**
     *  Register a route for a DELETE request
     */
    public static function delete(string $url, \Closure|string $callback, array $options = []): void
    {
        self::register('DELETE', $url, $callback, $options);
    }

    /**
     *  Register a route for any type of HTTP request
     */
    public static function any(string $url, \Closure|string $callback, array $options = []): void
    {
        self::register('ANY', $url, $callback, $options);
    }

    /**
     * Register a route to be returned if a 404 is encounted
     *
     * @param  string/Closure $callback Class@method or Closure to call
     * @param  array  $options
     */
    public static function register404Route(\Closure|string $callback, array $options = []): void
    {
        self::$_404route = new Route('ANY', '/404', $callback, $options);
    }

    /**
     * Register a group of routes
     */
    public static function group(array $properties, array $routes): void
    {
        // Translate a single filter to an array of one filter
        if (isset($properties['filter'])) {
            if (!is_array($properties['filter'])) {
                $properties['filter'] = [$properties['filter']];
            }
        } else {
            $properties['filter'] = [];
        }

        $baseProperties = ['prefix' => '', 'rprefix' => ''];
        $properties = array_merge($baseProperties, $properties);

        // $routes: [0] = HTTP method, [1] = pattern, [2] = controller/method route
        foreach ($routes as $route) {
            $httpmethod = $route[0];

            if (!method_exists(__CLASS__, $httpmethod)) {
                continue;
            }

            $pattern = $properties['prefix'].$route[1];
            $callback = $properties['rprefix'].$route[2];
            $options = [
                'filter' => $properties['filter']
            ];
            self::register(strtoupper($httpmethod), $pattern, $callback, $options);
        }
    }

    /**
     *  Common register function, adds route to $routeList
     */
    private static function register(string $method, string $url, \Closure|string $callback, array $options = []): void
    {
        $key = $method.'@'.$url;
        self::$routes[$key] = new Route($method, $url, $callback, $options);
    }

    /**
     *  Common register function, adds route to $routeList
     */
    public static function registerClass(string|object $obj): void
    {
        $class = new \ReflectionClass($obj);
        if (is_string($obj)) {
            $obj = $class->newInstance();
        }

        $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            $callback = $method->getClosure($obj);

            $attrs = $method->getAttributes('Onesimus\Router\Attr\Route');
            foreach ($attrs as $attr) {
                $a = $attr->newInstance();
                $key = $a->method.'@'.$a->pattern;

                self::$routes[$key] = new Route($a->method, $a->pattern, $callback);
            }

            $attrs = $method->getAttributes('Onesimus\Router\Attr\Route404');
            foreach ($attrs as $attr) {
                self::$_404route = new Route('ANY', '/404', $callback);
            }
        }
    }

    /**
     * Get array of current routes
     */
    public static function getRoutes(): array
    {
        return self::$routes;
    }

    /**
     *  Initiate the routing for the given URL
     */
    public static function route(Http\Request $request): Route
    {
        $path = $request->REQUEST_URI;
        $key = $request->getMethod().'@'.$path;
        $keyAny = 'ANY@'.$path;

        $matchedRoute = null;
        $matchedScore = 0;

        if (isset(self::$routes[$key])) {
            $matchedRoute = self::$routes[$key];
        } elseif (isset(self::$routes[$keyAny])) {
            $matchedRoute = self::$routes[$keyAny];
        } else {
            foreach (self::$routes as $key2 => $route) {
                if ($route->getHttpmethod() != 'ANY' && $route->getHttpmethod() != $request->getMethod()) {
                    continue;
                }

                $score = $route->getScore($path, $request->getMethod());
                if ($score > $matchedScore) {
                    $matchedRoute = $route;
                    $matchedScore = $score;
                }
            }
        }

        if (!$matchedRoute) {
            if (self::$_404route) {
                $matchedRoute = self::$_404route;
            } else {
                throw new Exceptions\RouteException('Route not found');
            }
        }

        return $matchedRoute;
    }

    /**
     * Register a filter with the router
     *
     * @param  string   $name     Name of the filter
     * @param  \Closure $callback Function to execute
     */
    public static function filter(string $name, \Closure $callback): void
    {
        self::$filters[$name] = $callback;
    }

    /**
     * Does the router have a particular filter
     *
     * @param  string  $name Name of filter to check
     * @return boolean
     */
    public static function hasFilter(string $name): bool
    {
        return array_key_exists($name, self::$filters);
    }

    /**
     * Execute filter $action
     */
    private static function handleFilter(string $action = '', array $args = []): bool
    {
        if (!$action) {
            return true;
        }

        if (self::hasFilter($action)) {
            $callback = self::$filters[$action];
            return call_user_func_array($callback, $args);
        }

        return false;
    }

    /**
     * Calls the closure or class/method pair assigned to route
     *
     * @param  mixed $params Parameters to pass to the class construction or closure
     *                       If a route calls a closure, $params is prepended to the
     *                       URL pattern values array. If the route calls a class
     *                       and method, $params is given the class upon construction
     *                       and only the pattern values are given to the method
     * @return mixed         Whatever is returned by the closure or method
     */
    public static function dispatch(mixed $params = null, Http\Request $request = null): void
    {
        if (is_null($request)) {
            $request = Http\Request::getRequest();
        }

        $route = self::route($request);

        if (!is_array($params)) {
            $params = [$params];
        }

        // Process filters
        if ($route->getFilters()) {
            foreach ($route->getFilters() as $filter) {
                if (!self::hasFilter($filter)) {
                    throw new Exceptions\UndefinedFilterException("Filter '{$filter}' not registered");
                }

                $filter_result = self::handleFilter($filter, $urlVars);
                if ($filter_result === false) {
                    throw new Exceptions\FailedFilterException($filter, $filter_result);
                }
            }
        }

        // Extract variables from URL
        $urlVars = $route->getVars($request->REQUEST_URI);

        // Call Closure if available
        if (!is_null($route->getCallable())) {
            if ($params) {
                $urlVars = array_merge($params, $urlVars);
            }
            $urlVars = array_merge([$request], $urlVars);

            $r = new \ReflectionFunction($route->getCallable());
            $a = $r->getAttributes('Onesimus\Router\Attr\Filter');

            foreach ($a as $attribute) {
                $c = $attribute->newInstance();

                foreach ($c->filters as $filter) {
                    if (!self::hasFilter($filter)) {
                        throw new Exceptions\UndefinedFilterException("Filter '{$filter}' not registered");
                    }

                    $filter_result = self::handleFilter($filter, $urlVars);

                    if ($filter_result === false) {
                        throw new Exceptions\FailedFilterException($filter, $filter_result);
                    }
                }
            }

            call_user_func_array($route->getCallable(), $urlVars);
            return;
        }

        $urlVars = array_merge([$request], $urlVars);

        // If no Closure, instantiate class
        if (!$route->getClass() || !class_exists($route->getClass())) {
            throw new Exceptions\RouteException("Controller '{$route->getClass()}' wasn't found.");
        }

        $controller = new ($route->getClass())($params);
        // Call class method
        if (method_exists($controller, $route->getHttpmethod()) && is_callable([$controller, $route->getHttpmethod()])) {
            $r = new \ReflectionMethod($controller, $route->getHttpmethod());
            $a = $r->getAttributes();

            foreach ($a as $attribute) {
                $c = $attribute->newInstance();
                if (!$c->route_check($request, $urlVars)) {
                    throw new Exceptions\FailedAttributeCheck("Attribute check failed");
                }
            }
            call_user_func_array([$controller, $route->getHttpmethod()], $urlVars);
            return;
        } else {
            throw new Exceptions\RouteException("Method '{$route->getHttpmethod()}' wasn't found in Class '{$route->getClass()}' or is not public.");
        }
    }
}
