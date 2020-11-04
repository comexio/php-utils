<?php

namespace Logcomex\PhpUtils\Middlewares;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Logcomex\PhpUtils\Dto\ResponseTimePayloadDto;
use Logcomex\PhpUtils\Enumerators\ErrorEnum;
use Logcomex\PhpUtils\Exceptions\BadImplementationException;

/**
 * Class ResponseTimeLogMiddleware
 * @package Logcomex\PhpUtils\Middlewares
 */
class ResponseTimeLogMiddleware
{
    /**
     * @var mixed
     */
    private $settings;

    /**
     * ResponseTimeLogMiddleware constructor.
     */
    public function __construct()
    {
        $this->settings = config('app');
    }

    /**
     * @param Request $request
     * @param Closure $next
     * @return JsonResponse
     * @throws BadImplementationException
     */
    public function handle(Request $request, Closure $next): JsonResponse
    {
        $response = $next($request);

        if (defined('GLOBAL_FRAMEWORK_START')) {
            $apiName = $this->getSetting('api-name');

            if (empty($apiName)) {
                throw new BadImplementationException(
                    ErrorEnum::PHU007,
                    'You must provide the "api-name" property in your app config.'
                );
            }

            $responseTime = microtime(true) - GLOBAL_FRAMEWORK_START;
            $responseTimeLogDto = new ResponseTimePayloadDto();
            $responseTimeLogDto->attachValues([
                'api' => $apiName,
                'endpoint' => $request->fullUrl(),
                'response_time' => $responseTime,
                'payload' => $request->all(),
                'created_at' => Carbon::now(),
            ]);
            $response->headers->set('Response-Time-Log', $responseTime);

            app('ResponseTimeLog')->save($responseTimeLogDto);
        }

        return response()->json($response);
    }

    /**
     * @param string $settingKey
     * @param string $defaultValue
     * @return string
     */
    public function getSetting(string $settingKey, string $defaultValue = ''): string
    {
        return $this->settings[$settingKey] ?? $defaultValue;
    }
}
