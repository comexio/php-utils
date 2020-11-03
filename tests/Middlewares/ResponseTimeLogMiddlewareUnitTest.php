<?php

use Illuminate\Http\Response;
use Logcomex\PhpUtils\Dto\ResponseTimePayloadDto;
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
        $this->expectExceptionToken($response->response->exception, 'PHU-007');
    }

    public function testRequestUsingMiddlewareWithoutGlobalFrameworkStart(): void
    {
        $response = $this->call('get', '/response-time-log-middleware');

        if (!defined('GLOBAL_FRAMEWORK_START')) {
            $this->assertNull($response->headers->get('response-time-log'));
        }

        $this->assertInstanceOf(Response::class, $response);
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
