<?php
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

if ($uri !== '/' && file_exists(__DIR__.'/App/Web' . $uri))
    return false;

require_once __DIR__.'/App/Web/Index.php';
