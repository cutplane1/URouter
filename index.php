<?php


class URouter
{
    public array $map;

    public function add($url, $callback)
    {
        $map[$url] = $callback;
    }
}

preg_match("/^\/users\/(\w+)$/", $_SERVER["REQUEST_URI"], $outr);

var_dump($outr);
