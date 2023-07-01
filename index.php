<?php


preg_match("/^\/users\/(\w+)$/", $_SERVER["REQUEST_URI"], $outr);

var_dump($outr);
