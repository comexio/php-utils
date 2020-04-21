<?php

namespace Logcomex\PhpUtils\Functionalities;

/**
 * Trait PropertiesExporterFunctionality
 * @package Logcomex\PhpUtils\Functionalities
 */
trait PropertiesExporterFunctionality
{
    /**
     * @return array
     */
    public static function properties(): array
    {
        return array_keys(get_class_vars(self::class));
    }
}
