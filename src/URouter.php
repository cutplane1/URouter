<?php

namespace Cutplane1;

class URouter
{
    public array $route_map;
    public array $middleware_map = [];

    public function route(string $pattern, mixed $callback): URouter
    {
        $pattern = $this->r2regex($pattern);
        $this->route_map[$pattern] = ["pattern" => $pattern, "callback" => $callback];

        return $this;
    }

    public function r2regex(string $r): string
    {
        $r = str_replace("/", "\/", $r);
        // not int
        $r = str_replace("#", "(\D+)", $r);
        // int
        $r = str_replace("@", "(\d+)", $r);

        return "/^". $r . "$/";
    }

    public function dispatch(string $ur): void
    {
        foreach($this->middleware_map as $middleware)
        {
            call_user_func($middleware);
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

    public function middleware(mixed $callback): URouter
    {
        array_push($this->middleware_map, $callback);

        return $this;
    }
}
