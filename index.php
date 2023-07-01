<?php


class URouter
{
    public array $map;

    public function add(string $pattern, callable $callback)
    {
        $this->map[$pattern] = ["pattern" => $pattern, "callback" => $callback];
    }

    public function dispatch()
    {
        foreach ($this->map as $pattern) 
        {
            echo $pattern["pattern"];
        }
    }
}

// preg_match("/^\/users\/(\w+)$/", $_SERVER["REQUEST_URI"], $outr);

// var_dump($outr);

$r = new URouter();

$r->add("/^\/users\/(\w+)$/", function($some_argument) {
    var_dump($some_argument);
});

$r->dispatch();