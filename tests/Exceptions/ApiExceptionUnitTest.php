<?php

use Logcomex\PhpUtils\Exceptions\ApiException;
use Logcomex\PhpUtils\Exceptions\BaseException;

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
        $exceptionArray = $exception->toArray();
        $this->assertIsArray($exceptionArray);
        $this->assertEquals('ApiException', $exceptionArray['exception-class']);
        $this->assertEquals('Test Message!', $exceptionArray['message']);
        $this->assertEquals('404', $exceptionArray['http-code']);
        $this->assertEquals('T001', $exceptionArray['token']);
        $this->assertNotNull($exceptionArray['file']);
        $this->assertNotNull($exceptionArray['line']);
    }

    /**
     * @depends testConstructor
     * @param ApiException $exception
     */
    public function testToJson(ApiException $exception): void
    {
        $exceptionJson = $exception->toJson();
        $this->assertJson($exceptionJson);

        $exceptionArray = json_decode($exceptionJson, true);
        $this->assertEquals('ApiException', $exceptionArray['exception-class']);
        $this->assertEquals('Test Message!', $exceptionArray['message']);
        $this->assertEquals('404', $exceptionArray['http-code']);
        $this->assertEquals('T001', $exceptionArray['token']);
        $this->assertNotNull($exceptionArray['file']);
        $this->assertNotNull($exceptionArray['line']);
    }

    /**
     * @depends testConstructor
     * @param ApiException $exception
     */
    public function testExtendsBaseException(ApiException $exception): void
    {
        $this->assertInstanceOf(BaseException::class, $exception);
    }
}
