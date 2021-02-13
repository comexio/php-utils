<?php


namespace Logcomex\PhpUtils\Exceptions;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

use Exception;

/**
 * Class BaseException
 * @package Logcomex\PhpUtils\Exceptions
 */
abstract class BaseException extends Exception implements Arrayable, Jsonable
{
    /**
     * @return string
     */
    abstract public function getToken(): string;
}