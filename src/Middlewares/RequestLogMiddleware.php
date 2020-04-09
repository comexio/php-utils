<?php

namespace Logcomex\PhpUtils\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Logcomex\PhpUtils\Logs\RequestLog;

/**
 * Class RequestLogMiddleware
 * @package Logcomex\PhpUtils\Middlewares
 */
class RequestLogMiddleware
{
    /**
     * @param $request
     * @param Closure $next
     */
    public function handle(Request $request, Closure $next)
    {
        $requestLog = new RequestLog();

        $requestLog
            ->setRequestHeaders($request->headers->all())
            ->setRequestServer($request->server->all())
            ->setRequestPayload($request->all());

        $response = $next($request);

        $responseTime = defined('GLOBAL_FRAMEWORK_START')
            ? microtime(true) - GLOBAL_FRAMEWORK_START
            : 'GLOBAL_FRAMEWORK_START is not setted';
        $requestLog
            ->setResponseHeaders($response->headers->all())
            ->setResponseContent($response->original)
            ->setResponseTime($responseTime);

        Log::info('teste', $requestLog->toArray());
    }
}
