<?php

namespace Logcomex\PhpUtils\Middlewares;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

/**
 * Class CorsMiddleware
 * @package Logcomex\PhpUtils\Middlewares
 */
class CorsMiddleware
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return JsonResponse|mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $corsSettings = collect(config('cors'))->filter();

        $allowOriginResponse = $this->treatAccessControlAllowOriginHeader($corsSettings, $request->getUri());
        $headers = [
            'Access-Control-Allow-Origin' => $allowOriginResponse,
            'Access-Control-Allow-Methods' => $corsSettings->get('access-control-allow-methods', 'POST, OPTIONS, PUT, DELETE, GET'),
            'Access-Control-Allow-Credentials' => $corsSettings->get('access-control-allow-credentials', 'true'),
            'Access-Control-Max-Age' => $corsSettings->get('access-control-max-age', '86400'),
            'Access-Control-Allow-Headers' => $corsSettings->get('access-control-allow-headers', 'Content-Type, Authorization, X-Requested-With, User-Email')
        ];

        if ($request->isMethod('OPTIONS')) {
            return response()->json(null, Response::HTTP_OK, $headers);
        }

        $response = $next($request);
        foreach ($headers as $key => $value) {
            $response->header($key, $value);
        }

        return $response;
    }

    /**
     * @param Collection $corsSettings
     * @param string $requestUri
     * @return string
     */
    public function treatAccessControlAllowOriginHeader(Collection $corsSettings, string $requestUri): string
    {
        $allowOriginResponse = '*';
        $allowAllOrigins = '*';
        $allowOriginBlocked = 'BLOCKED';
        $allowOriginRaw = $corsSettings->get('access-control-allow-origin', $allowAllOrigins);

        if ($allowOriginRaw !== $allowAllOrigins) {
            $allowOriginsOptions = explode(';', $allowOriginRaw);
            foreach ($allowOriginsOptions as $allowOriginOption) {
                $requestUriBelongsToAllowedHost = strpos($requestUri, $allowOriginOption) !== false;
                $allowOriginResponse = $requestUriBelongsToAllowedHost
                    ? $allowOriginOption
                    : $allowOriginBlocked;

                if ($allowOriginResponse !== $allowOriginBlocked) {
                    return $allowOriginResponse;
                }
            }
        }

        return $allowOriginResponse;
    }
}
