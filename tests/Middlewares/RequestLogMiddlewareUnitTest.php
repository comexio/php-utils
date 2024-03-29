<?php

namespace Tests\Middlewares;

use Exception;
use Logcomex\PhpUtils\Middlewares\RequestLogMiddleware;
use Tests\TestCase;

/**
 * Class RequestLogUnitTest
 */
class RequestLogMiddlewareUnitTest extends TestCase
{
    /**
     * @return void
     */
    public function testHandlerWithOutMicrotime(): void
    {
        try {
            $this->call('get', '/request-log-middleware');

            $this->assertTrue(true, 'Middleware is working!');
        } catch (Exception $exception) {
            $this->fail('Middleware is not working!');
        }
    }

    /**
     * @return void
     */
    public function testHandlerWithMicrotime(): void
    {
        config([
            'requestLog.enable-response-content' => true,
            'requestLog.allowed-data-request-server' => ['test'],
        ]);
        if (!defined('GLOBAL_FRAMEWORK_START')) {
            define('GLOBAL_FRAMEWORK_START', microtime(true));
        }
        try {
            $this->call('get', '/request-log-middleware');

            $this->assertTrue(true, 'Middleware is working!');
        } catch (Exception $exception) {
            $this->fail('Middleware is not working!');
        }
    }

    /**
     * @return void
     */
    public function testGetSetting(): void
    {
        $middleware = new RequestLogMiddleware();

        $response = $middleware->getSetting('test', true);
        $this->assertIsBool($response);
        $this->assertTrue($response);

        config([
            'requestLog.test' => false
        ]);
        $middleware->__construct();

        $response = $middleware->getSetting('test', true);
        $this->assertIsBool($response);
        $this->assertFalse($response);
    }
}
