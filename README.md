# u-router (1.2.2)
## Works with older php versions (>=5.3).
```php
<?php

require "vendor/autoload.php";

$router = new Cutplane1\URouter();

$router->not_found(function() {
    http_response_code(404);
    echo "<h1>404 Not Found</h1>";
});

$router->middleware(function() {
    header('Content-Type: application/json; charset=utf-8');
});

$router->get("/", function() {
    echo json_encode(["hello" => "world"]);
});

$router->get("/<int>", function($id) {
    echo json_encode(["id" => $id]);
});

$router->dispatch();
```