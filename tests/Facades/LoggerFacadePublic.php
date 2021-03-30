<?php

use Logcomex\PhpUtils\Facades\Logger;

/**
 * Class LoggerFacadePublic
 * @package Tests\Unit\Facades
 */
class LoggerFacadePublic extends Logger
{
    /**
     * @return string
     */
    public static function getFacadeAccessor(): string
    {
        return parent::getFacadeAccessor();
    }
}
