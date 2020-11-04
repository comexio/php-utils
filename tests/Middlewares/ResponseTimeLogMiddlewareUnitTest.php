<?php

use Logcomex\PhpUtils\Dto\ResponseTimePayloadDto;
use Logcomex\PhpUtils\Enumerators\ErrorEnum;
use Logcomex\PhpUtils\Exceptions\BadImplementationException;

/**
 * Class ResponseTimeLogMiddlewareUnitTest
 */
class ResponseTimeLogMiddlewareUnitTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        config([
            'app.api-name' => 'fakeApi',
        ]);

        app()->bind('ResponseTimeLog', function (): ResponseTimeLogFake {
            return new ResponseTimeLogFake();
        });
    }

    public function testRequestWithoutApiName(): void
    {
        if (!defined('GLOBAL_FRAMEWORK_START')) {
            define('GLOBAL_FRAMEWORK_START', microtime(true));
        }

        config([
            'app.api-name' => null,
        ]);

        $response = $this->get('/response-time-log-middleware');
        $this->assertResponseStatus(500);
        $this->assertInstanceOf(BadImplementationException::class, $response->response->exception);
        $this->expectExceptionToken($response->response->exception, ErrorEnum::PHU007);
    }

    public function testRequestUsingMiddlewareWithoutGlobalFrameworkStart(): void
    {
        $response = $this->call('get', '/response-time-log-middleware');
        $this->shouldReturnJson();

        if (!defined('GLOBAL_FRAMEWORK_START')) {
            $this->assertNull($response->original->headers->get('response-time-log'));
        }
    }

    public function testRequestUsingMiddlewareWithGlobalFrameworkStart(): void
    {
        if (!defined('GLOBAL_FRAMEWORK_START')) {
            define('GLOBAL_FRAMEWORK_START', microtime(true));
        }

        $response = $this->call('get', '/response-time-log-middleware');
        $this->shouldReturnJson();
        $this->assertGreaterThan(2, $response->original->headers->get('response-time-log'));
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
