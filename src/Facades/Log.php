<?php

namespace Logcomex\PhpUtils\Facades;

use Exception;
use Illuminate\Support\Facades\Facade;
use Logcomex\PhpUtils\Loggers\LogcomexLogger;

/**
 * Class Log
 * @package Logcomex\PhpUtils\Facades
 * @method static void error(string $token, $context = [], Exception $exception = null): void
 * @method static void info(string $token, $context = []): void
 * @method static void debug(string $message, $context = [], Exception $exception = null): void
 * @method static void severe(string $token, $context = [], Exception $exception = null): void
 * @method static LogcomexLogger channel(string $channelLog): LogcomexLogger
 *
 * @see LogcomexLogger
 */
class Log extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'Log';
    }
}
