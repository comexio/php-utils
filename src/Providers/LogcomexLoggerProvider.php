<?php

namespace Logcomex\PhpUtils\Providers;

use Illuminate\Support\ServiceProvider;
use Logcomex\PhpUtils\Loggers\LogcomexLogger;

/**
 * Class LogcomexLoggerProvider
 * @package Logcomex\PhpUtils\Providers
 */
class LogcomexLoggerProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->app->bind('Logger', function (): LogcomexLogger {
            return new LogcomexLogger();
        });
    }
}
