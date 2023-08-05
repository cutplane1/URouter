<?php

namespace UPack;

class URouter
{
    public string $default_req_uri;

    public array $routes = [];
    public array $middlewares = [];

    public function route(string $rule, mixed $callback, mixed $middleware): URouter
    {
        $pattern = $this->rule2regex($rule);
        array_push($this->routes, ["pattern" => $pattern, "callback" => $callback, "rule" => $rule]);

        return $this;
    }

    public function rule2regex(string $r): string
    {
        $r = str_replace("/", "\/", $r);
        // not int
        $r = str_replace("#", "(\w+)", $r);
        // int
        $r = str_replace("@", "(\d+)", $r);

        return "/^". $r . "$/";
    }

    public function dispatch(string $r = null): void
    {
        if(!$r) {$r = $this->default_req_uri;}
        foreach($this->middlewares as $middleware)
        {
            call_user_func($middleware);
        }

        foreach($this->routes as $pattern)
        {
            if (preg_match($pattern["pattern"], $r, $out))
            {
                array_shift($out);
                
                call_user_func_array($pattern["callback"], $out);
            }
        }
    }

    public function middleware(mixed $callback): URouter
    {
        array_push($this->middlewares, $callback);

        return $this;
    }

    function __construct()
    {
        if (php_sapi_name() === "cli") 
        {
            $this->default_req_uri = "/";
        } else
        {
            $this->default_req_uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
        }
        
    }
}
