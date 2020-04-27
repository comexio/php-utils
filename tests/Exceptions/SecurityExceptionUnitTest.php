<?php

use PHPUnit\Framework\TestCase;
use Logcomex\PhpUtils\Exceptions\SecurityException;

/**
 * Class SecurityExceptionUnitTest
 */
class SecurityExceptionUnitTest extends TestCase
{
    /**
     * @var string
     */
    private const EXCEPTION_TOKEN = 'SEC01';
    /**
     * @var string
     */
    private const EXCEPTION_MESSAGE = 'Test Message!';
    /**
     * @var int
     */
    private const EXCEPTION_HTTPCODE = 403;

    /**
     * @return SecurityException
     */
    public function testConstructor(): SecurityException
    {
        $exception = new SecurityException(
            self::EXCEPTION_TOKEN,
            self::EXCEPTION_MESSAGE,
            self::EXCEPTION_HTTPCODE
        );

        $this->assertInstanceOf(Exception::class, $exception);

        return $exception;
    }

    /**
     * @depends testConstructor
     * @param SecurityException $exception
     */
    public function testGetHttpCode(SecurityException $exception): void
    {
        $this->assertEquals(self::EXCEPTION_HTTPCODE, $exception->getHttpCode());
    }

    /**
     * @depends testConstructor
     * @param SecurityException $exception
     */
    public function testGetToken(SecurityException $exception): void
    {
        $this->assertEquals(self::EXCEPTION_TOKEN, $exception->getToken());
    }

    /**
     * @depends testConstructor
     * @param SecurityException $exception
     */
    public function test__toString(SecurityException $exception): void
    {
        $this->assertIsString($exception->__toString());

        $exceptionString = 'Logcomex\PhpUtils\Exceptions\SecurityException: [SEC01]: Test Message!';
        $this->assertEquals($exceptionString, $exception->__toString());
    }

    /**
     * @depends testConstructor
     * @param SecurityException $exception
     */
    public function testToArray(SecurityException $exception): void
    {
        $this->assertIsArray($exception->toArray());
        $this->assertEquals(
            '{"exception-class":"SecurityException","message":"Test Message!","file":"\/var\/www\/logcomex-php-utils\/tests\/Exceptions\/SecurityExceptionUnitTest.php","line":29,"http-code":403,"token":"SEC01"}',
            json_encode($exception->toArray())
        );
    }

    /**
     * @depends testConstructor
     * @param SecurityException $exception
     */
    public function testToJson(SecurityException $exception): void
    {
        $this->assertIsString($exception->toJson());
        $this->assertEquals(
            '{"exception-class":"SecurityException","message":"Test Message!","file":"\/var\/www\/logcomex-php-utils\/tests\/Exceptions\/SecurityExceptionUnitTest.php","line":29,"http-code":403,"token":"SEC01"}',
            $exception->toJson()
        );
    }
}
