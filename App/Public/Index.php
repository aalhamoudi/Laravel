<?php
define('LARAVEL_START', microtime(true));

require __DIR__.'/../../Vendor/autoload.php';

$app = \App\App::App();

$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
