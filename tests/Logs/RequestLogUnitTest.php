<?php

use PHPUnit\Framework\TestCase;
use Logcomex\PhpUtils\Logs\RequestLog;

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
        $requestLog = new RequestLog();
        $response = $requestLog->setRequestHeaders([]);

        $this->assertInstanceOf(RequestLog::class, $response);
    }

    /**
     * @return void
     */
    public function testRequestServer(): void
    {
        $requestLog = new RequestLog();
        $response = $requestLog->setRequestServer([]);

        $this->assertInstanceOf(RequestLog::class, $response);
    }

    /**
     * @return void
     */
    public function testRequestPayload(): void
    {
        $requestLog = new RequestLog();
        $response = $requestLog->setRequestPayload([]);

        $this->assertInstanceOf(RequestLog::class, $response);
    }

    /**
     * @return void
     */
    public function testResponseHeaders(): void
    {
        $requestLog = new RequestLog();
        $response = $requestLog->setResponseHeaders([]);

        $this->assertInstanceOf(RequestLog::class, $response);
    }

    /**
     * @return void
     */
    public function testResponseContent(): void
    {
        $requestLog = new RequestLog();
        $response = $requestLog->setResponseContent([]);

        $this->assertInstanceOf(RequestLog::class, $response);
    }

    /**
     * @return void
     */
    public function testResponseTime(): void
    {
        $requestLog = new RequestLog();
        $response = $requestLog->setResponseTime([]);

        $this->assertInstanceOf(RequestLog::class, $response);
    }

    /**
     * @return void
     */
    public function testToArray(): void
    {
        $requestLog = new RequestLog();
        $response = $requestLog->toArray();

        $this->assertIsArray($response);
        $this->assertEquals(
            '{"request":{"headers":null,"server":null,"payload":null},"response":{"headers":null,"content":null,"execution-time":null}}',
            json_encode($response)
        );
    }

    /**
     * @return void
     */
    public function testToJson(): void
    {
        $requestLog = new RequestLog();
        $response = $requestLog->toJson();

        $this->assertIsString($response);
        $this->assertEquals(
            '{"request":{"headers":null,"server":null,"payload":null},"response":{"headers":null,"content":null,"execution-time":null}}',
            $response
        );
    }

    /**
     * @return void
     */
    public function test__toString(): void
    {
        $requestLog = new RequestLog();
        $response = $requestLog->__toString();

        $this->assertIsString($response);
        $this->assertEquals(
            '{"request":{"headers":null,"server":null,"payload":null},"response":{"headers":null,"content":null,"execution-time":null}}',
            $response
        );
    }
}
