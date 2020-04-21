<?php

namespace Logcomex\PhpUtils\Middlewares;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * Class CorsMiddleware
 * @package Logcomex\PhpUtils\Middlewares
 */
class CorsMiddleware
{
    /**
     * @param $request
     * @param Closure $next
     * @return JsonResponse|mixed
     */
    public function handle($request, Closure $next)
    {
        $corsSettings = collect(config('cors'))->filter();
        $headers = [
            'Access-Control-Allow-Origin' => $corsSettings->get('access-control-allow-origin', '*'),
            'Access-Control-Allow-Methods' => $corsSettings->get('access-control-allow-methods', 'POST, OPTIONS, PUT, DELETE'),
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
}
