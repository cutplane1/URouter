# u-router

```php
<?php

require "vendor/autoload.php";

$router = new Cutplane\URouter();

$router->middleware(function() {
    header('Content-Type: application/json; charset=utf-8');
});

$router->route("/", function() {
    echo json_encode(["hello" => "world"]);
});

$router->route("/#", function($id) {
    echo json_encode(["id" => $id]);
});

$router->dispatch($_SERVER["REQUEST_URI"]);
```
