<?php

use Illuminate\Http\Response;

/**
 * Class AllowedHostsMiddlewareUnitTest
 */
class AllowedHostsMiddlewareUnitTest extends TestCase
{
    /**
     * @return void
     */
    public function testHandlerSuccess(): void
    {
        config([
            'app.allowed-hosts' => [

            ],
        ]);
        $response = $this->call('get', '/allowed-hosts-middleware');
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), "Middleware is not working!");

        config([
            'app.allowed-hosts' => [
                'localhost'
            ],
        ]);
        $response = $this->call('get', '/allowed-hosts-middleware');
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), "Middleware is not working!");
    }

    /**
     * @return void
     */
    public function testHandlerFailure(): void
    {
        config([
            'app.allowed-hosts' => [
                'myfakehost.com'
            ],
        ]);
        $response = $this->call('get', '/allowed-hosts-middleware');
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode(), "Middleware is not working!");
    }
}
