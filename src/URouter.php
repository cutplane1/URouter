<?php

namespace UPack;

class URouter
{
    public string $default_req_uri;

    public array $routes = [];

    public array $middlewares = [];

    public function route(string $rule, callable $callback, mixed $middleware = null): URouter
    {
        $pattern = $this->rule2regex($rule);
        array_push($this->routes, ['pattern' => $pattern, 'callback' => $callback, 'rule' => $rule, 'middleware' => $middleware]);

        return $this;
    }

    public function rule2regex(string $rule): string
    {
        $rule = str_replace('/', "\/", $rule);
        // not int
        $rule = str_replace('#', "(\w+)", $rule);
        // int
        $rule = str_replace('@', "(\d+)", $rule);

        return '/^'.$rule.'$/';
    }

    public function dispatch(string $uri = null): void
    {
        if (!$uri) {
            $uri = $this->default_req_uri;
        }
        foreach ($this->middlewares as $middleware) {
            call_user_func($middleware);
        }

        foreach ($this->routes as $route) {
            if (preg_match($route['pattern'], $uri, $out)) {
                array_shift($out);
                if ($route['middleware']) {
                    call_user_func($route['middleware']);
                }
                call_user_func_array($route['callback'], $out);
            }
        }
    }

    public function middleware(callable $callback): URouter
    {
        array_push($this->middlewares, $callback);

        return $this;
    }

    public function __construct()
    {
        if ('cli' === php_sapi_name()) {
            $this->default_req_uri = '/';
        } else {
            $this->default_req_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        }
    }

    public function group(array $routes, callable $middleware = null) // : URouter
    {
        foreach ($routes as $rule => $callback) {
            $pattern = $this->rule2regex($rule);
            array_push($this->routes, ['pattern' => $pattern, 'callback' => $callback, 'rule' => $rule, 'middleware' => $middleware]);
        }

        return $this;
    }
}
