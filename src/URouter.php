<?php

namespace Cutplane1;

/**
 * Routing Class.
 */
class URouter
{
    /**
     * Error callback.
     */
    public $error_callback;

    /**
     * Request URI.
     */
    public $default_req_uri;

    /**
     * Invokes a callback.
     */
    public $is_found = false;

    /**
     * Array of routes.
     */
    public $routes = array();

    /**
     * Array of middlewares.
     */
    public $middlewares = array();

    public function __construct()
    {
        if ('cli' === php_sapi_name()) {
            $_SERVER['REQUEST_METHOD'] = "GET";
            $this->default_req_uri = '/';
        } else {
            $this->default_req_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        }
    }

    public function any($rule, $callback, $middleware = null)
    {
        $this->route($rule, $callback, $middleware, "ANY");
    }

    /**
     * GET HTTP Verb.
     */
    public function get($rule, $callback, $middleware = null)
    {
        $this->route($rule, $callback, $middleware, "GET");
    }
    /**
     * POST HTTP Verb.
     */
    public function post($rule, $callback, $middleware = null)
    {
        $this->route($rule, $callback, $middleware, "POST");
    }
    /**
     * PUT HTTP Verb.
     */
    public function put($rule, $callback, $middleware = null)
    {
        $this->route($rule, $callback, $middleware, "PUT");
    }
    /**
     * PATCH HTTP Verb.
     */
    public function patch($rule, $callback, $middleware = null)
    {
        $this->route($rule, $callback, $middleware, "PATCH");
    }
    /**
     * DELETE HTTP Verb.
     */
    public function delete($rule, $callback, $middleware = null)
    {
        $this->route($rule, $callback, $middleware, "DELETE");
    }
    /**
     * OPTIONS HTTP Verb.
     */
    public function options($rule, $callback, $middleware = null)
    {
        $this->route($rule, $callback, $middleware, "OPTIONS");
    }

    /**
     * Adds route to array.
     */
    public function route($rule, $callback, $middleware = null, $verb)
    {
        $pattern = $this->rule2regex($rule);
        array_push($this->routes, array('pattern' => $pattern, 'callback' => $callback, 'rule' => $rule, 'middleware' => $middleware, 'verb' => $verb));

        return $this;
    }
    /**
     * Adds a route group to an array.
     */
    public function group($routes, $middleware = null)
    {
        foreach ($routes as $rule => $callback) {
            $pattern = $this->rule2regex($rule);
            array_push($this->routes, array('pattern' => $pattern, 'callback' => $callback, 'rule' => $rule, 'middleware' => $middleware));
        }

        return $this;
    }

    /**
     * Turns an easy-to-read rule into a regular expression.
     */
    public function rule2regex($rule)
    {
        $rule = str_replace('/', "\/", $rule);
        $rule = str_replace(array("<any>", "<str>", "<string>", "<#>"), "(\w+)", $rule);
        $rule = str_replace(array("<int>", "<integer>", "<@>"), "(\d+)", $rule);

        return '/^'.$rule.'$/';
    }

    /**
     * Adds middleware to array.
     */
    public function middleware($callback)
    {
        array_push($this->middlewares, $callback);

        return $this;
    }

    /**
     * Executes callback on error.
     */
    public function handle_error()
    {
        if ($this->error_callback) {
            call_user_func($this->error_callback);
        } else {
            http_response_code(404);
            echo "404";
        }
    }

    /**
     * Sets error callback.
     */
    public function not_found($callback)
    {
        $this->error_callback = $callback;
    }

    /**
     * Executes a callback when a route is found.
     */
    public function dispatch($uri = null)
    {
        if (!$uri) {
            $uri = $this->default_req_uri;
        }

        foreach ($this->routes as $route) {
            $match = preg_match($route['pattern'], $uri, $out);
            if ($match and $route['verb'] === $_SERVER['REQUEST_METHOD'] or $match and $route['verb'] === "ANY") {
                foreach ($this->middlewares as $middleware) {
                    call_user_func($middleware);
                }
                array_shift($out);
                if ($route['middleware']) {
                    call_user_func($route['middleware']);
                }
                call_user_func_array($route['callback'], $out);
                $this->is_found = true;
            }
        }

        if (!$this->is_found) {
            $this->handle_error();
        }
    }
}
