<?php

use Logcomex\PhpUtils\Dto\ResponseTimePayloadDto;

/**
 * Class ResponseTimeLogUnitTest
 */
class ResponseTimeLogMiddlewareUnitTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        config([
            'responseTimeLog.api-name' => 'fakeApi',
        ]);

        app()->bind('ResponseTimeLog', function (): ResponseTimeLogFake {
            return new ResponseTimeLogFake();
        });
    }

    public function testRequestUsingMiddlewareWithoutGlobalFrameworkStart(): void
    {
        $response = $this->call('get', '/response-time-log-middleware');

        if (!defined('GLOBAL_FRAMEWORK_START')) {
            $this->assertNull($response->headers->get('response-time-log'));
        }

        $this->assertResponseOk();
    }

    public function testRequestUsingMiddlewareWithGlobalFrameworkStart(): void
    {
        if (!defined('GLOBAL_FRAMEWORK_START')) {
            define('GLOBAL_FRAMEWORK_START', microtime(true));
        }

        $response = $this->call('get', '/response-time-log-middleware');
        $this->assertGreaterThan(2, $response->headers->get('response-time-log'));
    }
}

/**
 * Class ResponseTimeLogFake
 */
class ResponseTimeLogFake
{
    public function save(ResponseTimePayloadDto $responseTimePayloadDto): void
    {
    }
}
