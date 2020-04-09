<?php

use PHPUnit\Framework\TestCase;
use Logcomex\PhpUtils\Exceptions\ApiException;

/**
 * Class ApiExceptionUnitTest
 */
class ApiExceptionUnitTest extends TestCase
{
    /**
     * @var string
     */
    private const EXCEPTION_TOKEN = 'T001';
    /**
     * @var string
     */
    private const EXCEPTION_MESSAGE = 'Test Message!';
    /**
     * @var int
     */
    private const EXCEPTION_HTTPCODE = 404;

    /**
     * @return ApiException
     */
    public function testConstructor(): ApiException
    {
        $exception = new ApiException(
            self::EXCEPTION_TOKEN,
            self::EXCEPTION_MESSAGE,
            self::EXCEPTION_HTTPCODE
        );

        $this->assertInstanceOf(Exception::class, $exception);

        return $exception;
    }

    /**
     * @depends testConstructor
     * @param ApiException $exception
     */
    public function testGetHttpCode(ApiException $exception): void
    {
        $this->assertEquals(self::EXCEPTION_HTTPCODE, $exception->getHttpCode());
    }

    /**
     * @depends testConstructor
     * @param ApiException $exception
     */
    public function testGetToken(ApiException $exception): void
    {
        $this->assertEquals(self::EXCEPTION_TOKEN, $exception->getToken());
    }

    /**
     * @depends testConstructor
     * @param ApiException $exception
     */
    public function test__toString(ApiException $exception): void
    {
        $this->assertIsString($exception->__toString());

        $exceptionString = 'Logcomex\PhpUtils\Exceptions\ApiException: [T001]: Test Message!';
        $this->assertEquals($exceptionString, $exception->__toString());
    }

    /**
     * @depends testConstructor
     * @param ApiException $exception
     */
    public function testToArray(ApiException $exception): void
    {
        $this->assertIsArray($exception->toArray());
        $this->assertEquals(
            '{"exception-class":"ApiException","message":"Test Message!","file":"\/var\/www\/logcomex-php-utils\/tests\/ApiExceptionUnitTest.php","line":29,"http-code":404,"token":"T001"}',
            json_encode($exception->toArray())
        );
    }

    /**
     * @depends testConstructor
     * @param ApiException $exception
     */
    public function testToJson(ApiException $exception): void
    {
        $this->assertIsString($exception->toJson());
        $this->assertEquals(
            '{"exception-class":"ApiException","message":"Test Message!","file":"\/var\/www\/logcomex-php-utils\/tests\/ApiExceptionUnitTest.php","line":29,"http-code":404,"token":"T001"}',
            $exception->toJson()
        );
    }
}
