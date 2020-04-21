<?php

namespace Logcomex\PhpUtils\Helpers;

/**
 * Class TokenHelper
 */
class TokenHelper
{
    /**
     * @param bool $short
     * @return string
     */
    public static function generate(bool $short = true): string
    {
        return $short ? uniqid() : md5(uniqid(rand(), true));
    }
}
