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
     * @var mixed
     */
    private $settings;

    /**
     * RequestLogMiddleware constructor.
     */
    public function __construct()
    {
        $this->settings = config('requestLog');
    }

    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $requestLog = new RequestLog();
        if ($this->getSetting('enable-request-header', true)) {
            $requestLog->setRequestHeaders($request->headers->all());
        }
        if ($this->getSetting('enable-request-server', true)) {
            $allowedDataSetting = $this->getSetting('allowed-data-request-server');
            $requestServerContent = $request->server->all();

            if (isset($allowedDataSetting)) {
                $requestServerContent = collect($requestServerContent)
                    ->only($allowedDataSetting)
                    ->toArray();
            }

            $requestLog->setRequestServer($requestServerContent);
        }
        if ($this->getSetting('enable-request-payload', true)) {
            $requestLog->setRequestPayload($request->all());
        }

        $response = $next($request);

        if ($this->getSetting('enable-response-header', true)) {
            $requestLog->setResponseHeaders($response->headers->all());
        }
        if ($this->getSetting('enable-response-content', false)) {
            $requestLog->setResponseContent($response->original);
        }
        if ($this->getSetting('enable-response-time', true)) {
            $responseTime = defined('GLOBAL_FRAMEWORK_START')
                ? microtime(true) - GLOBAL_FRAMEWORK_START
                : 'GLOBAL_FRAMEWORK_START is not setted';
            $requestLog->setResponseTime($responseTime);
        }

        Log::info('[[REQUEST_INFO]]', $requestLog->toArray());

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
