<?php

namespace Tests\Exceptions;

use Exception;
use Logcomex\PhpUtils\Exceptions\BaseException;
use Logcomex\PhpUtils\Exceptions\UnavailableServiceException;
use Tests\TestCase;

/**
 * Class UnavailableServiceExceptionUnitTest
 */
class UnavailableServiceExceptionUnitTest extends TestCase
{
    /**
     * @var string
     */
    private const EXCEPTION_TOKEN = 'SE001';
    /**
     * @var string
     */
    private const EXCEPTION_MESSAGE = 'Test Message!';
    /**
     * @var string
     */
    private const EXCEPTION_SERVICE = 'Service 1';
    /**
     * @var int
     */
    private const EXCEPTION_HTTPCODE = 503;

    /**
     * @return UnavailableServiceException
     */
    public function testConstructor(): UnavailableServiceException
    {
        $exception = new UnavailableServiceException(
            self::EXCEPTION_TOKEN,
            self::EXCEPTION_MESSAGE,
            self::EXCEPTION_SERVICE,
            self::EXCEPTION_HTTPCODE
        );

        $this->assertInstanceOf(Exception::class, $exception);

        return $exception;
    }

    /**
     * @depends testConstructor
     * @param UnavailableServiceException $exception
     */
    public function testGetHttpCode(UnavailableServiceException $exception): void
    {
        $this->assertEquals(self::EXCEPTION_HTTPCODE, $exception->getHttpCode());
    }

    /**
     * @depends testConstructor
     * @param UnavailableServiceException $exception
     */
    public function testGetToken(UnavailableServiceException $exception): void
    {
        $this->assertEquals(self::EXCEPTION_TOKEN, $exception->getToken());
    }

    /**
     * @depends testConstructor
     * @param UnavailableServiceException $exception
     */
    public function test__toString(UnavailableServiceException $exception): void
    {
        $this->assertIsString($exception->__toString());

        $exceptionString = 'Logcomex\PhpUtils\Exceptions\UnavailableServiceException: [SE001]: Test Message!';
        $this->assertEquals($exceptionString, $exception->__toString());
    }

    /**
     * @depends testConstructor
     * @param UnavailableServiceException $exception
     */
    public function testToArray(UnavailableServiceException $exception): void
    {
        $exceptionArray = $exception->toArray();
        $this->assertIsArray($exceptionArray);
        $this->assertEquals('UnavailableServiceException', $exceptionArray['exception-class']);
        $this->assertEquals('Test Message!', $exceptionArray['reason']);
        $this->assertEquals('503', $exceptionArray['http-code']);
        $this->assertEquals('SE001', $exceptionArray['token']);
        $this->assertNotNull($exceptionArray['file']);
        $this->assertNotNull($exceptionArray['line']);
    }

    /**
     * @depends testConstructor
     * @param UnavailableServiceException $exception
     */
    public function testToJson(UnavailableServiceException $exception): void
    {
        $exceptionJson = $exception->toJson();
        $this->assertJson($exceptionJson);

        $exceptionArray = json_decode($exceptionJson, true);
        $this->assertEquals('UnavailableServiceException', $exceptionArray['exception-class']);
        $this->assertEquals('Test Message!', $exceptionArray['reason']);
        $this->assertEquals('503', $exceptionArray['http-code']);
        $this->assertEquals('SE001', $exceptionArray['token']);
        $this->assertNotNull($exceptionArray['file']);
        $this->assertNotNull($exceptionArray['line']);
    }

    /**
     * @depends testConstructor
     * @param UnavailableServiceException $exception
     */
    public function testGetService(UnavailableServiceException $exception): void
    {
        $this->assertEquals(self::EXCEPTION_SERVICE, $exception->getService());
    }

    /**
     * @depends testConstructor
     * @param UnavailableServiceException $exception
     */
    public function testExtendsBaseException(UnavailableServiceException $exception): void
    {
        $this->assertInstanceOf(BaseException::class, $exception);
    }
}
