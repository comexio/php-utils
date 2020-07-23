<?php

namespace Logcomex\PhpUtils\Middlewares;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class AuthenticateMiddleware
 * @package Logcomex\PhpUtils\Middlewares
 */
class AuthenticateMiddleware
{
    /**
     * The authentication guard factory instance.
     *
     * @var Auth
     */
    protected $auth;

    /**
     * @param Auth $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string|null $guard
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function handle($request, Closure $next, $guard = null): JsonResponse
    {
        $isNotAuthenticated = $this->auth->guard($guard)->guest();
        if ($isNotAuthenticated) {
            throw new AuthenticationException('Unauthorized');
        }

        return $next($request);
    }
}
