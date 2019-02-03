<?php

namespace App;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

if ($mode = 'Laravel')
    class_alias(\Illuminate\Foundation\Exceptions\Handler::class, ExceptionHandler::class);
else
    class_alias(\Laravel\Lumen\Exceptions\Handler::class, ExceptionHandler::class);

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];


    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    public function render($request, Exception $exception)
    {
        return parent::render($request, $exception);
    }
}
