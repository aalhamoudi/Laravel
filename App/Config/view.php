<?php

if (!function_exists('app_path')) {
    function app_path($name = '')
    {
        return base_path('App') . $name;
    }
}

return [
    'paths' => [
        app_path('Views'),
    ],
    'compiled' => env('VIEW_COMPILED_PATH', realpath(storage_path('framework/views')))
];
