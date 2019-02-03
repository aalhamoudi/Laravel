<?php
namespace App;

require_once __DIR__ . '/../Vendor/autoload.php';

class App extends Application
{
    protected static $app;

    public function __construct($base)
    {
        parent::__construct($base);
        $this->router->group(['namespace' => 'App\Controllers'], function($router) {require __DIR__.'/Routes/web.php'; });
    }
}
