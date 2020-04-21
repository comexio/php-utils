<?php

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
            $this->assertTrue(false, 'Middleware is not working!');
        }
    }

    /**
     * @return void
     */
    public function testHandlerWithMicrotime(): void
    {
        if (!defined('GLOBAL_FRAMEWORK_START')) {
            define('GLOBAL_FRAMEWORK_START', microtime(true));
        }
        try {
            $this->call('get', '/request-log-middleware');

            $this->assertTrue(true, 'Middleware is working!');
        } catch (Exception $exception) {
            $this->assertTrue(false, 'Middleware is not working!');
        }
    }
}
