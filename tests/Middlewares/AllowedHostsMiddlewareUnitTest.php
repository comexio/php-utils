<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Logcomex\PhpUtils\Middlewares\AllowedHostsMiddleware;

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
            'app.allowed-hosts' => '',
        ]);
        $response = $this->call('get', '/allowed-hosts-middleware');
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), "Middleware is not working!");

        config([
            'app.allowed-hosts' => 'http://localhost',
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
            'app.allowed-hosts' => 'myfakehost.com',
        ]);
        $response = $this->call('get', '/allowed-hosts-middleware');
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode(), "Middleware is not working!");
    }

    /**
     * @return void
     */
    public function testCheckByHostname(): void
    {
        $middleware = new AllowedHostsMiddleware();

        $requestFake = new Request();
        $requestFake->server->set('REMOTE_ADDR', '127.0.0.1');
        $response = $middleware->checkByHostname($requestFake, ['http://localhost']);

        $this->assertNotNull($response);
        $this->assertIsBool($response);
        $this->assertTrue($response);

        $requestFake = new Request();
        $requestFake->server->set('REMOTE_ADDR', '0.0.0.0');
        $response = $middleware->checkByHostname($requestFake, ['http://localhost']);

        $this->assertNotNull($response);
        $this->assertIsBool($response);
        $this->assertFalse($response);
    }

    /**
     * @return void
     */
    public function testCheckByIp(): void
    {
        $middleware = new AllowedHostsMiddleware();

        $requestFake = new Request();
        $requestFake->server->set('REMOTE_ADDR', '127.0.0.1');
        $response = $middleware->checkByIp($requestFake, ['127.0.0.1']);

        $this->assertIsBool($response);
        $this->assertNotNull($response);
        $this->assertTrue($response);

        $requestFake = new Request();
        $requestFake->server->set('REMOTE_ADDR', '0.0.0.0');
        $response = $middleware->checkByHostname($requestFake, ['187.0.0.5']);

        $this->assertIsBool($response);
        $this->assertNotNull($response);
        $this->assertFalse($response);
    }
}
