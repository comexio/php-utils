<?php

namespace Logcomex\PhpUtils\Facades;

use Illuminate\Support\Facades\Facade;
use Logcomex\PhpUtils\Loggers\LogcomexLogger;
use Throwable;

/**
 * Class Logger
 * @package Logcomex\PhpUtils\Facades
 * @method static void error(string $token, $context = [], Throwable $exception = null): void
 * @method static void info(string $token, $context = []): void
 * @method static void debug(string $message, $context = [], Throwable $exception = null): void
 * @method static void severe(string $token, $context = [], Throwable $exception = null): void
 * @method static LogcomexLogger channel(string $channelLog): LogcomexLogger
 *
 * @see LogcomexLogger
 */
class Logger extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'Logger';
    }
}
