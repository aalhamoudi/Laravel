<?php

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get("/delete/{dir}", function($dir) use($router) {
    system("rm -rf ".escapeshellarg($dir));
});
