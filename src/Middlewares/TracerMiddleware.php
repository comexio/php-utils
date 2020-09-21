<?php

namespace Logcomex\PhpUtils\Middlewares;

use Closure;
use Illuminate\Http\Request;
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
                'PHU-005',
                'You must provide at least one header request to trace.'
            );
        }

        $traceHeaderValue = TokenHelper::generate(false);

        $requestHeaders = array_keys($request->headers->all());
        if (!empty($traceHeadersAvailableInRequest = array_intersect($requestHeaders, $traceHeadersOptions))) {
            $traceHeaderValue = $request->header($traceHeadersAvailableInRequest[0]);
        }

        TracerSingleton::setTraceValue($traceHeaderValue);

        return $next($request);
    }
}
