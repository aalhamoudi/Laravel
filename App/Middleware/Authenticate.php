<?php

namespace App\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

use Illuminate\Contracts\Auth\Factory as Auth;



class Authenticate
{

    protected $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate($request, $guards);

        return $next($request);
    }

    protected function authenticate($request, $guards)
    {
        $guards = !isset($guards) || $guards === null? [] : (is_array($guards)? $guards : [$guards]);

        if (empty($guards)) {
            $guards = [null];
        }

        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->guest())
                return response('Unauthorized.', 401);

            if ($this->auth->guard($guard)->check())
                return $this->auth->shouldUse($guard);

        }

        throw new AuthenticationException('Unauthenticated.', $guards, $this->redirectTo($request));
    }

    protected function redirectTo($request)
    {
        if (!$request->expectsJson())
            return route('login');

    }
}
