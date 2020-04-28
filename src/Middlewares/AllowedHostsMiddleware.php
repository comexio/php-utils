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
        $allowedHosts = array_filter(explode(';', config('app.allowed-hosts', '')));

        $isAllowedByHostname = $this->checkByHostname($request, $allowedHosts);
        $isAllowedByIp = $this->checkByIp($request, $allowedHosts);

        $isAllowedHost = empty($allowedHosts) || $isAllowedByHostname || $isAllowedByIp;
        if ($isAllowedHost) {
            $response = $next($request);

            return $response;
        }

        throw new SecurityException('SEC001', "Host is not allowed to use this API.", Response::HTTP_FORBIDDEN);
    }

    /**
     * @param Request $request
     * @param array $allowedHosts
     * @return bool
     */
    public function checkByHostname(Request $request, array $allowedHosts): bool
    {
        $requestOriginScheme = $request->getScheme();
        $requestOriginHost = gethostbyaddr($request->getClientIp());
        $requestOriginFull = "{$requestOriginScheme}://{$requestOriginHost}";

        return in_array($requestOriginFull, $allowedHosts);
    }

    /**
     * @param Request $request
     * @param array $allowedHosts
     * @return bool
     */
    public function checkByIp(Request $request, array $allowedHosts): bool
    {
        $requestOriginFull = $request->getClientIp();

        return in_array($requestOriginFull, $allowedHosts);
    }
}
