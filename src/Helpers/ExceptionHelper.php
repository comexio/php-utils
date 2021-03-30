<?php

namespace Logcomex\PhpUtils\Helpers;

use Throwable;

/**
 * Class ExceptionHelper
 * @package Logcomex\PhpUtils\Helpers
 */
class ExceptionHelper
{
    /**
     * @param Throwable $exception
     * @return array
     */
    public static function exportExceptionToArray(Throwable $exception): array
    {
        if (method_exists($exception, 'toArray')) {
            return $exception->toArray();
        }

        return [
            'exception-class' => class_basename($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'code' => $exception->getCode(),
        ];
    }
}
