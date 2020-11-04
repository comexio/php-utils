<?php

namespace Logcomex\PhpUtils\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Logcomex\PhpUtils\Enumerators\ErrorEnum;
use Logcomex\PhpUtils\Exceptions\BadImplementationException;
use Logcomex\PhpUtils\Helpers\TokenHelper;
use Logcomex\PhpUtils\Singletons\TracerSingleton;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TracerMiddleware
 * @package Logcomex\PhpUtils\Middlewares
 */
class TracerMiddleware
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws BadImplementationException
     */
    public function handle(Request $request, Closure $next): Response
    {
        $traceHeadersOptions = config('tracer.headersToPropagate');
        if (empty($traceHeadersOptions)) {
            throw new BadImplementationException(
                ErrorEnum::PHU005,
                'You must provide at least one header name to trace.'
            );
        }
        $traceHeadersOptions = is_array($traceHeadersOptions)
            ? $traceHeadersOptions
            : [$traceHeadersOptions];
        array_map('strtolower', $traceHeadersOptions);

        $traceHeaderValue = TokenHelper::generate(false);

        $requestHeaders = array_map('strtolower', array_keys($request->headers->all()));
        if (!empty($traceHeadersAvailableInRequest = array_intersect($requestHeaders, $traceHeadersOptions))) {
            $traceHeaderKey = array_shift($traceHeadersAvailableInRequest);
            $traceHeaderValue = $request->header($traceHeaderKey);
        }

        TracerSingleton::setTraceValue($traceHeaderValue);

        return $next($request);
    }
}
