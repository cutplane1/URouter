<?php


class URouter
{
    public array $map;

    public function add(string $pattern, callable $callback): void
    {
        $this->map[$pattern] = ["pattern" => $pattern, "callback" => $callback];
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

// preg_match("/^\/users\/(\w+)$/", $_SERVER["REQUEST_URI"], $outr);

// var_dump($outr);

$r = new URouter();

$r->add("/^\/users\/(\w+)$/", function($some_argument) {
    var_dump($some_argument);
});

$r->dispatch($_SERVER["REQUEST_URI"]);