<?php
define('LARAVEL_START', microtime(true));

require __DIR__.'/../../Vendor/autoload.php';

/** @var \App\App $app */
$app = \App\App::App(__DIR__ . '/../../');

$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle($request = Illuminate\Http\Request::capture());
$response->send();

$kernel->terminate($request, $response);
