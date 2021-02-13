<?php

use Illuminate\Http\Response;
use Logcomex\PhpUtils\Exceptions\BadImplementationException;
use Logcomex\PhpUtils\Exceptions\BaseException;

/**
 * Class BadImplementationExceptionUnitTest
 */
class BadImplementationExceptionUnitTest extends TestCase
{
    /**
     * @return BadImplementationException
     */
    public function testConstructor(): BadImplementationException
    {
        $exception = new BadImplementationException(
            'BI001',
            'Error!'
        );

        $this->assertInstanceOf(Exception::class, $exception);

        return $exception;
    }

    /**
     * @depends testConstructor
     * @param BadImplementationException $exception
     */
    public function testGetHttpCode(BadImplementationException $exception): void
    {
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getHttpCode());
    }

    /**
     * @depends testConstructor
     * @param BadImplementationException $exception
     */
    public function test__toString(BadImplementationException $exception): void
    {
        $this->assertIsString($exception->__toString());

        $exceptionString = 'Logcomex\PhpUtils\Exceptions\BadImplementationException: [500]: Error!';
        $this->assertEquals($exceptionString, $exception->__toString());
    }

    /**
     * @depends testConstructor
     * @param BadImplementationException $exception
     */
    public function testToArray(BadImplementationException $exception): void
    {
        $exceptionArray = $exception->toArray();
        $this->assertIsArray($exceptionArray);
        $this->assertEquals('BadImplementationException', $exceptionArray['exception-class']);
        $this->assertEquals('Error!', $exceptionArray['message']);
        $this->assertEquals('500', $exceptionArray['http-code']);
        $this->assertNotNull($exceptionArray['file']);
        $this->assertNotNull($exceptionArray['line']);
    }

    /**
     * @depends testConstructor
     * @param BadImplementationException $exception
     */
    public function testToJson(BadImplementationException $exception): void
    {
        $exceptionJson = $exception->toJson();
        $this->assertJson($exceptionJson);

        $exceptionArray = json_decode($exceptionJson, true);
        $this->assertEquals('BadImplementationException', $exceptionArray['exception-class']);
        $this->assertEquals('Error!', $exceptionArray['message']);
        $this->assertEquals('500', $exceptionArray['http-code']);
        $this->assertNotNull($exceptionArray['file']);
        $this->assertNotNull($exceptionArray['line']);
    }

    /**
     * @depends testConstructor
     * @param BadImplementationException $exception
     */
    public function testExtendsBaseException(BadImplementationException $exception): void
    {
        $this->assertInstanceOf(BaseException::class, $exception);
    }
}
