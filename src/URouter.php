<?php

namespace Cutplane1;

class URouter
{
    public $error_callback;

    public string $default_req_uri = "/";

    public bool $is_found = false;

    public array $routes = [];

    public array $middlewares = [];

    public function get(string $rule, callable $callback, mixed $middleware = null) {$this->route($rule, $callback, $middleware, "GET");}
    public function post(string $rule, callable $callback, mixed $middleware = null) {$this->route($rule, $callback, $middleware, "POST");}
    public function put(string $rule, callable $callback, mixed $middleware = null) {$this->route($rule, $callback, $middleware, "PUT");}
    public function patch(string $rule, callable $callback, mixed $middleware = null) {$this->route($rule, $callback, $middleware, "PATCH");}
    public function delete(string $rule, callable $callback, mixed $middleware = null) {$this->route($rule, $callback, $middleware, "DELETE");}
    public function options(string $rule, callable $callback, mixed $middleware = null) {$this->route($rule, $callback, $middleware, "OPTIONS");}
    
    public function route(string $rule, callable $callback, mixed $middleware = null, string $verb = "GET"): URouter
    {
        $pattern = $this->rule2regex($rule);
        array_push($this->routes, ['pattern' => $pattern, 'callback' => $callback, 'rule' => $rule, 'middleware' => $middleware, 'verb' => $verb]);

        return $this;
    }

    public function rule2regex(string $rule): string
    {
        $rule = str_replace('/', "\/", $rule);
        $rule = str_replace(["<any>", "<str>", "<string>", "<#>"], "(\w+)", $rule);
        $rule = str_replace(["<int>", "<integer>", "<@>"], "(\d+)", $rule);

        return '/^'.$rule.'$/';
    }

    public function dispatch(string $uri = null): void
    {
        if (!$uri) {
            $uri = $this->default_req_uri;
        }

        foreach ($this->routes as $route) {
            if (preg_match($route['pattern'], $uri, $out) and $route['verb'] === $_SERVER['REQUEST_METHOD']) {
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

        if (!$this->is_found) {$this->handle_error();}
    }

    public function middleware(callable $callback): URouter
    {
        array_push($this->middlewares, $callback);

        return $this;
    }

    public function __construct()
    {
        if ('cli' === php_sapi_name()) {
            $_SERVER['REQUEST_METHOD'] = "GET";
            $this->default_req_uri = '/';
        } else {
            $this->default_req_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        }
    }

    public function group(array $routes, callable $middleware = null): URouter
    {
        foreach ($routes as $rule => $callback) {
            $pattern = $this->rule2regex($rule);
            array_push($this->routes, ['pattern' => $pattern, 'callback' => $callback, 'rule' => $rule, 'middleware' => $middleware]);
        }

        return $this;
    }

    public function handle_error()
    {
        if ($this->error_callback) {
            call_user_func($this->error_callback);
        } else {
            http_response_code(404);
            echo "404";
        }
    }

    public function not_found(callable $callback)
    {
        $this->error_callback = $callback;
    }
}
