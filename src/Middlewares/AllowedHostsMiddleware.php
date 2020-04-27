<?php

namespace Logcomex\PhpUtils\Middlewares;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Logcomex\PhpUtils\Exceptions\SecurityException;

/**
 * Class AllowedHostsMiddleware
 * @package Logcomex\PhpUtils\Middlewares
 */
class AllowedHostsMiddleware
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return JsonResponse|mixed
     * @throws SecurityException
     */
    public function handle(Request $request, Closure $next)
    {
        $requestOriginHost = $request->getHost();
        $allowedHosts = config('app.allowed-hosts', []);

        $isAllowedHost = empty($allowedHosts) || in_array($requestOriginHost, $allowedHosts);
        if ($isAllowedHost) {
            $response = $next($request);

            return $response;
        }

        throw new SecurityException('SEC001', "Host {$requestOriginHost} is not allowed to use this API.", Response::HTTP_FORBIDDEN);
    }
}
