<?php

namespace Logcomex\PhpUtils\Helpers;

use Exception;
use ReflectionClass;

/**
 * Trait EnumHelper
 * @package Logcomex\PhpUtils\Helpers
 */
trait EnumHelper
{
    /**
     * @return array
     */
    public static function all(): array
    {
        try {
            return (new ReflectionClass(get_class()))->getConstants();
            // @codeCoverageIgnoreStart
        } catch (Exception $exception) {
            return [];
            // @codeCoverageIgnoreEnd
        }
    }
}
