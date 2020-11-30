<?php

namespace Logcomex\PhpUtils\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Logcomex\PhpUtils\Exceptions\SecurityException;
use Symfony\Component\HttpFoundation\Response;

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
    public function handle(Request $request, Closure $next): Response
    {
        $requestUriBasePath = $this->extractRequestBasePath($request->getRequestUri());
        $isPublicRoute = preg_match('/public/', $requestUriBasePath);

        if ($isPublicRoute) {
            return $next($request);
        }

        $xInfraKeyHeader = $request->header('x-infra-key') || $request->header('x-api-key', '');
        $accreditedApiKeys = config('accreditedApiKeys', []);

        $this->validateXApiKeyData($accreditedApiKeys, $xInfraKeyHeader);

        $response = $next($request);
        $response = $this->setWelcomeHeaderInResponse($response, $accreditedApiKeys, $xInfraKeyHeader);

        return $response;
    }

    /**
     * @param Response $response
     * @param array $accreditedApiKeys
     * @param string $xApiKeyHeader
     * @return Response
     */
    public function setWelcomeHeaderInResponse(Response $response,
                                               array $accreditedApiKeys,
                                               string $xApiKeyHeader): Response
    {
        $accreditedApiKeys = array_filter($accreditedApiKeys);
        $xApiKeyName = array_flip($accreditedApiKeys)[$xApiKeyHeader];
        $apiName = config('app.api-name', 'UnknowApi');
        $response->headers->set('Welcome-Message', "Welcome to the {$apiName}. You are using the x-infra-key credential: {$xApiKeyName}");

        return $response;
    }

    /**
     * @param array $accreditedApiKeys
     * @param string $xInfraKeyHeader
     * @throws SecurityException
     */
    public function validateXApiKeyData(array $accreditedApiKeys, string $xInfraKeyHeader): void
    {
        if (empty($xInfraKeyHeader)) {
            throw new SecurityException(
                'SEC03',
                'This endpoint is protected by InfraKey, you must provide a valid x-infra-key header to use.',
                Response::HTTP_UNAUTHORIZED
            );
        }

        if (!in_array($xInfraKeyHeader, $accreditedApiKeys)) {
            throw new SecurityException(
                'SEC04',
                'Your x-infra-key header is invalid.',
                Response::HTTP_UNAUTHORIZED
            );
        }
    }

    /**
     * @param string $requestUri
     * @return string
     */
    public function extractRequestBasePath(string $requestUri): string
    {
        $requestUriExploded = explode('/', $requestUri);
        $requestUriExploded = array_filter($requestUriExploded);

        return array_shift($requestUriExploded) ?? '';
    }
}
