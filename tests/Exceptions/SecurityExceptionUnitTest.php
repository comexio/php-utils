<?php

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
        $exceptionArray = $exception->toArray();
        $this->assertIsArray($exceptionArray);
        $this->assertEquals('SecurityException', $exceptionArray['exception-class']);
        $this->assertEquals('Test Message!', $exceptionArray['message']);
        $this->assertEquals('403', $exceptionArray['http-code']);
        $this->assertEquals('SEC01', $exceptionArray['token']);
        $this->assertNotNull($exceptionArray['file']);
        $this->assertNotNull($exceptionArray['line']);
    }

    /**
     * @depends testConstructor
     * @param SecurityException $exception
     */
    public function testToJson(SecurityException $exception): void
    {
        $exceptionJson = $exception->toJson();
        $this->assertJson($exceptionJson);

        $exceptionArray = json_decode($exceptionJson, true);
        $this->assertEquals('SecurityException', $exceptionArray['exception-class']);
        $this->assertEquals('Test Message!', $exceptionArray['message']);
        $this->assertEquals('403', $exceptionArray['http-code']);
        $this->assertEquals('SEC01', $exceptionArray['token']);
        $this->assertNotNull($exceptionArray['file']);
        $this->assertNotNull($exceptionArray['line']);
    }
}
