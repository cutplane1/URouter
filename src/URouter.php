<?php

namespace Cutplane;

class URouter
{
    public array $routes = [];
    public array $middlewares = [];

    public function route(string $rule, mixed $callback): URouter
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

    public function dispatch(string $r): void
    {
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
}
