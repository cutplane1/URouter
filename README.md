# u-router

```php
<?php

$items = [
    [
        "id" => 1,
        "title" => "example1",
        "price" => 229,
    ],    
    [
        "id" => 2,
        "title" => "example2",
        "price" => 82,
    ],    
    [
        "id" => 3,
        "title" => "example3",
        "price" => 1499,
    ]
];

require "vendor/autoload.php";

use Cutplane1\URouter;

$router = new URouter();

$router->middleware(function() {
    header('Content-Type: application/json; charset=utf-8');
});

$router->route("/", function() use ($items) {
    echo json_encode($items);
});

$router->route("/@", function($id) use ($items) {
    echo json_encode($items[$id]);
});

$router->dispatch($_SERVER["REQUEST_URI"]);
```
