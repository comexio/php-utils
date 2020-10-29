<?php

namespace Logcomex\PhpUtils\Middlewares;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
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
        $this->settings = config('responseTimeLog');
    }

    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws BadImplementationException
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (defined('GLOBAL_FRAMEWORK_START')) {
            $responseTime = microtime(true) - GLOBAL_FRAMEWORK_START;
            $responseTimeLogDto = new ResponseTimePayloadDto();
            $responseTimeLogDto->attachValues([
                'api' => $this->getSetting('api-name'),
                'endpoint' => $request->fullUrl(),
                'response_time' => $responseTime,
                'payload' => $request->all(),
                'created_at' => Carbon::now(),
            ]);
            $response->header('Response-Time', $responseTime);

            app('ResponseTimeLog')->save($responseTimeLogDto);
        }

        return $response;
    }

    /**
     * @param string $settingKey
     * @param null $defaultValue
     * @return mixed
     */
    public function getSetting(string $settingKey, $defaultValue = null)
    {
        return $this->settings[$settingKey] ?? $defaultValue;
    }
}
