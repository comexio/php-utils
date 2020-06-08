<?php

namespace Logcomex\PhpUtils\Middlewares;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Logcomex\PhpUtils\Exceptions\SecurityException;

/**
 * Class AccreditedApiKeysMiddleware
 * @package Logcomex\PhpUtils\Middlewares
 */
class AccreditedApiKeysMiddleware
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws SecurityException
     */
    public function handle(Request $request, Closure $next): JsonResponse
    {
        $xApiKeyHeader = $request->header('x-api-key');
        $accreditedApiKeys = config('accreditedApiKeys', []);

        $this->validateXApiKeyData($accreditedApiKeys, $xApiKeyHeader);

        $response = $next($request);
        $response = $this->setWelcomeHeaderInResponse($response, $accreditedApiKeys, $xApiKeyHeader);

        return $response;
    }

    /**
     * @param JsonResponse $response
     * @param array $accreditedApiKeys
     * @param string $xApiKeyHeader
     * @return JsonResponse
     */
    public function setWelcomeHeaderInResponse(JsonResponse $response,
                                               array $accreditedApiKeys,
                                               string $xApiKeyHeader): JsonResponse
    {
        $xApiKeyName = array_flip($accreditedApiKeys)[$xApiKeyHeader];
        $apiName = config('app.api-name', 'UnknowApi');
        $response->headers->set('Welcome-Message', "Welcome to the {$apiName}. You are using the x-api-key credential: {$xApiKeyName}");

        return $response;
    }

    /**
     * @param array $accreditedApiKeys
     * @param string $xApiKeyHeader
     * @throws SecurityException
     */
    public function validateXApiKeyData(array $accreditedApiKeys, string $xApiKeyHeader): void
    {
        if (empty($xApiKeyHeader)) {
            throw new SecurityException(
                'SEC03',
                'This endpoint is protected by ApiKey, you must provide a valid x-api-key header to use.',
                Response::HTTP_UNAUTHORIZED
            );
        }

        if (!in_array($xApiKeyHeader, $accreditedApiKeys)) {
            throw new SecurityException(
                'SEC04',
                'Your x-api-key header is invalid.',
                Response::HTTP_UNAUTHORIZED
            );
        }
    }
}
