# u-router

```php
<?php

require "vendor/autoload.php";

$router = new Cutplane1\URouter();

$router->add("pdo", new \PDO("sqlite::memory:"));

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

$router->get("/<int>", function($ctx) {
    echo json_encode(["id" => $ctx->args[0]]);
});

$router->get("/sqlite_version", function($ctx) {
    $d = $ctx->pdo->query("SELECT SQLITE_VERSION()");
    echo $d->fetch()[0];
});

$router->dispatch();
```