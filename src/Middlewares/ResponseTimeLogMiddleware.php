<?php

namespace Logcomex\PhpUtils\Middlewares;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Logcomex\PhpUtils\Dto\ResponseTimePayloadDto;
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
     * @return Response
     * @throws BadImplementationException
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (defined('GLOBAL_FRAMEWORK_START')) {
            $apiName = $this->getSetting('api-name');

            if (empty($apiName)) {
                throw new BadImplementationException(
                    'PHU-007',
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

        return $response;
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
