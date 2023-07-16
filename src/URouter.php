<?php

namespace Cutplane1;

class URouter
{
    public array $route_map, $middleware_map;

    public function route(string $pattern, mixed $callback): void
    {
        $pattern = $this->r2regex($pattern);
        $this->route_map[$pattern] = ["pattern" => $pattern, "callback" => $callback];
    }

    public function r2regex(string $r): string
    {
        $r = str_replace("/", "\/", $r);
        $r = str_replace("#", "(\D+)", $r);
        $r = str_replace("@", "(\d+)", $r);
        return "/^". $r . "$/";
    }

    public function dispatch(string $ur)
    {
        foreach($this->middleware_map as $middleware)
        {
            echo $middleware;
        }

        foreach($this->route_map as $pattern)
        {
            if (preg_match($pattern["pattern"], $ur, $outr))
            {
                array_shift($outr);
                call_user_func_array($pattern["callback"], $outr);
            }
        }
    }

    public function middleware(mixed $callback): void
    {
        array_push($this->middleware_map, $callback);
    }
}
