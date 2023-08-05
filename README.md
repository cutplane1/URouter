# u-router

```php
<?php

require "vendor/autoload.php";

$router = new Cutplane1\URouter();

$router->handle_error(function() {
    http_response_code(404);
    echo "<h1>404 Not Found</h1>";
});

$router->middleware(function() {
    header('Content-Type: application/json; charset=utf-8');
});

$router->route("/", function() {
    echo json_encode(["hello" => "world"]);
});

$router->route("/<int>", function($id) {
    echo json_encode(["id" => $id]);
});

$router->dispatch();
```
