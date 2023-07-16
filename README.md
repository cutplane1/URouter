# u-router

```php
$posts = [
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

$router->route("/", function() {
    global $posts;
    echo json_encode($posts);
});

// @ => (\d+)
$router->route("/@", function($id) {
    global $posts;
    echo json_encode($posts[$id]);
});

$router->dispatch($_SERVER["REQUEST_URI"]);
```
