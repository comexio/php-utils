<?php

namespace Logcomex\PhpUtils\Loggers;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Log;
use Logcomex\PhpUtils\Helpers\ExceptionHelper;
use Logcomex\PhpUtils\Singletons\TracerSingleton;
use Throwable;

/**
 * Class LogcomexLogger
 * @package Logcomex\PhpUtils\Loggers
 */
class LogcomexLogger
{
    /**
     * @var string
     */
    private $channelLog;

    /**
     * @param array $context
     * @param Throwable|null $exception
     * @return array
     */
    public static function treatContext($context = [], Throwable $exception = null): array
    {
        $context = $context instanceof Arrayable
            ? $context->toArray()
            : (array)$context;

        if (!is_null($exception)) {
            $context = array_merge($context, ExceptionHelper::exportExceptionToArray($exception));
        }

        $context = json_decode(json_encode($context), true);
        array_walk_recursive($context, function (&$value) {
            if (is_string($value)) {
                $value = trim(str_replace("\n", " ", $value));
            }
        });

        return $context;
    }

    /**
     * @param string $token
     * @param array $context
     */
    public function info(string $token, $context = []): void
    {
        $traceId = TracerSingleton::getTraceValue();
        $context = self::treatContext($context);
        Log::channel($this->channelLog)->info("[[INFO]] | {$traceId} | ({$token})", $context);
    }

    /**
     * @param string $token
     * @param array $context
     * @param Throwable|null $exception
     */
    public function error(string $token, $context = [], Throwable $exception = null): void
    {
        $traceId = TracerSingleton::getTraceValue();
        $context = self::treatContext($context, $exception);
        Log::channel($this->channelLog)->error("[[ERROR]] | {$traceId} | ({$token})", $context);
    }

    /**
     * @param string $message
     * @param array $context
     * @param Throwable|null $exception
     */
    public function debug(string $message, $context = [], Throwable $exception = null): void
    {
        $traceId = TracerSingleton::getTraceValue();
        $context = self::treatContext($context, $exception);
        Log::channel($this->channelLog)->debug("[[DEBUG]] | {$traceId} | ({$message})", $context);
    }

    /**
     * @param string $token
     * @param array $context
     * @param Throwable|null $exception
     */
    public function severe(string $token, $context = [], Throwable $exception = null): void
    {
        $traceId = TracerSingleton::getTraceValue();
        $context = self::treatContext($context, $exception);
        Log::channel($this->channelLog)->error("[[SEVERE]] | {$traceId} | ({$token})", $context);
    }

    /**
     * @param string $channelLog
     * @return $this
     */
    public function channel(string $channelLog): LogcomexLogger
    {
        $this->channelLog = $channelLog;

        return $this;
    }
}
