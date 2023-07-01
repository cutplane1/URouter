<?php

class URouter
{
    public array $map;

    public function add(string $pattern, callable $callback): void
    {
        $pattern = $this->r2regex($pattern);
        $this->map[$pattern] = ["pattern" => $pattern, "callback" => $callback];
    }

    public function r2regex(string $r): string
    {
        $r = str_replace("/", "\/", $r);
        $r = str_replace("#", "(\w+)", $r);
        return "/^". $r . "$/";
    }

    public function dispatch(string $ur)
    {
        foreach ($this->map as $pattern)
        {
            preg_match($pattern["pattern"], $ur, $outr);
            array_shift($outr);
            call_user_func_array($pattern["callback"], $outr);
        }
    }
}

/* EXAMPLE:
$r = new URouter();

$r->add("/users/#/#", function($some_argument, $some_argument2) {
    var_dump($some_argument, $some_argument2);
});

$r->dispatch($_SERVER["REQUEST_URI"]);
*/
