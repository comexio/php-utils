<?php

namespace Tests\Facades;

use Logcomex\PhpUtils\Facades\Log;

/**
 * Class LogFacadePublic
 * @package Tests\Unit\Facades
 */
class LogFacadePublic extends Log
{
    /**
     * @return string
     */
    public static function getFacadeAccessor(): string
    {
        return parent::getFacadeAccessor();
    }
}
