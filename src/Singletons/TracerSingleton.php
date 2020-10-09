<?php

namespace Logcomex\PhpUtils\Singletons;

/**
 * Class TracerSingleton
 * @package Logcomex\PhpUtils\Singletons
 */
class TracerSingleton
{
    private static $traceValue = '';

    /**
     * @return mixed
     */
    public static function getTraceValue(): string
    {
        if (empty(self::$traceValue)) {
            return 'TRACE_NOT_IMPLEMENTED';
        }

        return self::$traceValue;
    }

    /**
     * @param string $traceValue
     */
    public static function setTraceValue(string $traceValue): void
    {
        self::$traceValue = $traceValue;
    }
}
