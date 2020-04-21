<?php

use Illuminate\Http\Response;
use Logcomex\PhpUtils\Exceptions\BadImplementationException;
use PHPUnit\Framework\TestCase;

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
        $exception = new BadImplementationException('Error!', Response::HTTP_INTERNAL_SERVER_ERROR);

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
        $this->assertIsArray($exception->toArray());
        $this->assertEquals(
            '{"exception-class":"BadImplementationException","message":"Error!","file":"\/var\/www\/logcomex-php-utils\/tests\/Exceptions\/BadImplementationExceptionUnitTest.php","line":17,"http-code":500}',
            json_encode($exception->toArray())
        );
    }

    /**
     * @depends testConstructor
     * @param BadImplementationException $exception
     */
    public function testToJson(BadImplementationException $exception): void
    {
        $this->assertIsString($exception->toJson());
        $this->assertEquals(
            '{"exception-class":"BadImplementationException","message":"Error!","file":"\/var\/www\/logcomex-php-utils\/tests\/Exceptions\/BadImplementationExceptionUnitTest.php","line":17,"http-code":500}',
            $exception->toJson()
        );
    }
}
