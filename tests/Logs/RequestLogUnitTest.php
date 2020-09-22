<?php

use PHPUnit\Framework\TestCase;
use Logcomex\PhpUtils\Logs\RequestInfoLog;

/**
 * Class RequestLogUnitTest
 */
class RequestLogUnitTest extends TestCase
{
    /**
     * @return void
     */
    public function testRequestHeaders(): void
    {
        $requestLog = new RequestInfoLog();
        $response = $requestLog->setRequestHeaders([]);

        $this->assertInstanceOf(RequestInfoLog::class, $response);
    }

    /**
     * @return void
     */
    public function testRequestServer(): void
    {
        $requestLog = new RequestInfoLog();
        $response = $requestLog->setRequestServer([]);

        $this->assertInstanceOf(RequestInfoLog::class, $response);
    }

    /**
     * @return void
     */
    public function testRequestPayload(): void
    {
        $requestLog = new RequestInfoLog();
        $response = $requestLog->setRequestPayload([]);

        $this->assertInstanceOf(RequestInfoLog::class, $response);
    }

    /**
     * @return void
     */
    public function testResponseHeaders(): void
    {
        $requestLog = new RequestInfoLog();
        $response = $requestLog->setResponseHeaders([]);

        $this->assertInstanceOf(RequestInfoLog::class, $response);
    }

    /**
     * @return void
     */
    public function testResponseContent(): void
    {
        $requestLog = new RequestInfoLog();
        $response = $requestLog->setResponseContent([]);

        $this->assertInstanceOf(RequestInfoLog::class, $response);
    }

    /**
     * @return void
     */
    public function testResponseTime(): void
    {
        $requestLog = new RequestInfoLog();
        $response = $requestLog->setResponseTime([]);

        $this->assertInstanceOf(RequestInfoLog::class, $response);
    }

    /**
     * @return void
     */
    public function testSetTraceId(): void
    {
        $requestLog = new RequestInfoLog();
        $response = $requestLog->setTraceId('test');

        $this->assertInstanceOf(RequestInfoLog::class, $response);
    }

    /**
     * @return void
     */
    public function testToArray(): void
    {
        $requestLog = new RequestInfoLog();
        $response = $requestLog->toArray();

        $this->assertIsArray($response);
        $this->assertEquals(
            '{"request":{"headers":null,"server":null,"payload":null,"trace-id":null},"response":{"headers":null,"content":null,"execution-time":null}}',
            json_encode($response)
        );
    }

    /**
     * @return void
     */
    public function testToJson(): void
    {
        $requestLog = new RequestInfoLog();
        $response = $requestLog->toJson();

        $this->assertIsString($response);
        $this->assertEquals(
            '{"request":{"headers":null,"server":null,"payload":null,"trace-id":null},"response":{"headers":null,"content":null,"execution-time":null}}',
            $response
        );
    }

    /**
     * @return void
     */
    public function test__toString(): void
    {
        $requestLog = new RequestInfoLog();
        $response = $requestLog->__toString();

        $this->assertIsString($response);
        $this->assertEquals(
            '{"request":{"headers":null,"server":null,"payload":null,"trace-id":null},"response":{"headers":null,"content":null,"execution-time":null}}',
            $response
        );
    }
}
