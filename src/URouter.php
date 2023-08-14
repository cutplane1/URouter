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
    public string $default_req_uri;

    /**
     * Invokes a callback.
     */
    public bool $is_found = false;

    /**
     * Array of routes.
     */
    public array $routes = [];

    /**
     * Array of middlewares.
     */
    public array $middlewares = [];

    public Context $context;

    public function __construct()
    {
        if ('cli' === php_sapi_name()) {
            $_SERVER['REQUEST_METHOD'] = "GET";
            $this->default_req_uri = '/';
        } else {
            $this->default_req_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        }

        $this->context = new Context();
    }

    public function any(string $rule, callable $callback, mixed $middleware = null)
    {
        $this->route($rule, $callback, $middleware, "ANY");
    }

    /**
     * GET HTTP Verb.
     */
    public function get(string $rule, callable $callback, mixed $middleware = null)
    {
        $this->route($rule, $callback, $middleware, "GET");
    }
    /**
     * POST HTTP Verb.
     */
    public function post(string $rule, callable $callback, mixed $middleware = null)
    {
        $this->route($rule, $callback, $middleware, "POST");
    }
    /**
     * PUT HTTP Verb.
     */
    public function put(string $rule, callable $callback, mixed $middleware = null)
    {
        $this->route($rule, $callback, $middleware, "PUT");
    }
    /**
     * PATCH HTTP Verb.
     */
    public function patch(string $rule, callable $callback, mixed $middleware = null)
    {
        $this->route($rule, $callback, $middleware, "PATCH");
    }
    /**
     * DELETE HTTP Verb.
     */
    public function delete(string $rule, callable $callback, mixed $middleware = null)
    {
        $this->route($rule, $callback, $middleware, "DELETE");
    }
    /**
     * OPTIONS HTTP Verb.
     */
    public function options(string $rule, callable $callback, mixed $middleware = null)
    {
        $this->route($rule, $callback, $middleware, "OPTIONS");
    }

    /**
     * Adds route to array.
     */
    public function route(string $rule, callable $callback, mixed $middleware = null, string $verb): URouter
    {
        $pattern = $this->rule2regex($rule);
        array_push($this->routes, ['pattern' => $pattern, 'callback' => $callback, 'rule' => $rule, 'middleware' => $middleware, 'verb' => $verb]);

        return $this;
    }
    /**
     * Adds a route group to an array.
     */
    public function group(array $routes, callable $middleware = null): URouter
    {
        foreach ($routes as $rule => $callback) {
            $pattern = $this->rule2regex($rule);
            array_push($this->routes, ['pattern' => $pattern, 'callback' => $callback, 'rule' => $rule, 'middleware' => $middleware]);
        }

        return $this;
    }

    /**
     * Turns an easy-to-read rule into a regular expression.
     */
    public function rule2regex(string $rule): string
    {
        $rule = str_replace('/', "\/", $rule);
        $rule = str_replace(["<any>", "<str>", "<string>", "<#>"], "(\w+)", $rule);
        $rule = str_replace(["<int>", "<integer>", "<@>"], "(\d+)", $rule);

        return '/^'.$rule.'$/';
    }

    /**
     * Adds middleware to array.
     */
    public function middleware(callable $callback): URouter
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
    public function not_found(callable $callback): URouter
    {
        $this->error_callback = $callback;

        return $this;
    }

    /**
     * Executes a callback when a route is found.
     */
    public function dispatch(string $uri = null): void
    {
        if (!$uri) {
            $uri = $this->default_req_uri;
        }

        foreach ($this->routes as $route) {
            $match = preg_match($route['pattern'], $uri, $out);
            array_shift($out);
            $this->context->args = $out;
            if ($match and $route['verb'] === $_SERVER['REQUEST_METHOD'] or $match and $route['verb'] === "ANY") {
                foreach ($this->middlewares as $middleware) {
                    call_user_func($middleware);
                }

                // new Context() > setout
                if ($route['middleware']) {
                    call_user_func($route['middleware']);
                }
                call_user_func($route['callback'], $this->context);
                $this->is_found = true;
            }
        }

        if (!$this->is_found) {
            $this->handle_error();
        }
    }

    public function add($name, $obj)
    {
        $this->context->$name = $obj;
    }
}

class Context
{
    public array $args;

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}