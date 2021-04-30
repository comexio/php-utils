<?php

use Logcomex\PhpUtils\Middlewares\CorsMiddleware;

/**
 * Class CorsMiddlewareUnitTest
 */
class CorsMiddlewareUnitTest extends TestCase
{
    /**
     * @return void
     */
    public function testTreatAccessControlAllowOriginHeader(): void
    {
        $middleware = new CorsMiddleware();

        $corsSettings = collect(['access-control-allow-origin' => 'http://localhost',]);
        $response = $middleware->treatAccessControlAllowOriginHeader($corsSettings, 'http://localhost');
        $this->assertIsString($response);
        $this->assertEquals('http://localhost', $response);

        $corsSettings = collect(['access-control-allow-origin' => 'http://localhost',]);
        $response = $middleware->treatAccessControlAllowOriginHeader($corsSettings, 'http://localhost/my-random-path');
        $this->assertIsString($response);
        $this->assertEquals('http://localhost', $response);

        $corsSettings = collect(['access-control-allow-origin' => '*',]);
        $response = $middleware->treatAccessControlAllowOriginHeader($corsSettings, 'http://localhost');
        $this->assertIsString($response);
        $this->assertEquals('*', $response);

        $corsSettings = collect(['access-control-allow-origin' => 'http://localhost',]);
        $response = $middleware->treatAccessControlAllowOriginHeader($corsSettings, 'http://tsohlacol');
        $this->assertIsString($response);
        $this->assertEquals('BLOCKED', $response);
    }

    /**
     * @return void
     */
    public function testHandlerSuccess(): void
    {
        config([
            'cors.access-control-allow-origin' => 'http://localhost',
            'cors.access-control-allow-methods' => 'a',
            'cors.access-control-allow-credentials' => 'a',
            'cors.access-control-max-age' => 'a',
            'cors.access-control-allow-headers' => 'a',
        ]);

        $httpVerbsToTest = ['get', 'options', 'post', 'patch', 'delete'];
        foreach ($httpVerbsToTest as $httpVerb) {
            try {
                $response = $this->call($httpVerb, '/cors-middleware');

                $responseHeaders = $response->headers->all();
                $expectedHeadersValues = [
                    'access-control-allow-origin' => 'http://localhost',
                    'access-control-allow-methods' => 'a',
                    'access-control-allow-credentials' => 'a',
                    'access-control-max-age' => 'a',
                    'access-control-allow-headers' => 'a',
                ];

                foreach ($expectedHeadersValues as $expectedHeader => $expectedHeaderValue) {
                    $this->assertTrue(
                        array_key_exists($expectedHeader, $responseHeaders),
                        "Response haven't the expected header: {$expectedHeader}"
                    );

                    $headerValue = $responseHeaders[$expectedHeader][0];
                    $this->assertEquals(
                        $expectedHeaderValue,
                        $headerValue,
                        "Response header value is not correct: {$headerValue}"
                    );
                }

                $this->assertTrue(true, "Middleware is working in '{$httpVerb}' http verb!");
            } catch (Exception $exception) {
                $this->assertTrue(false, "Middleware is not working in '{$httpVerb}' http verb!");
            }
        }
    }

    /**
     * @return void
     */
    public function testHandlerFailure(): void
    {
        config([
            'cors.access-control-allow-origin' => 'http://myrandomdomain',
            'cors.access-control-allow-methods' => 'a',
            'cors.access-control-allow-credentials' => 'a',
            'cors.access-control-max-age' => 'a',
            'cors.access-control-allow-headers' => 'a',
        ]);

        $httpVerbsToTest = ['get', 'options', 'post', 'patch', 'delete'];
        foreach ($httpVerbsToTest as $httpVerb) {
            try {
                $response = $this->call($httpVerb, '/cors-middleware');

                $responseHeaders = $response->headers->all();

                $this->assertTrue(
                    array_key_exists('access-control-allow-origin', $responseHeaders),
                    "Response haven't the expected header: access-control-allow-origin"
                );

                $headerValue = $responseHeaders['access-control-allow-origin'][0];
                $this->assertEquals(
                    'BLOCKED',
                    $headerValue,
                    "Response header value is not correct: {$headerValue}"
                );

                $this->assertTrue(true, "Middleware is working in '{$httpVerb}' http verb!");
            } catch (Exception $exception) {
                $this->assertTrue(false, "Middleware is not working in '{$httpVerb}' http verb!");
            }
        }
    }

    /**
     * @return void
     */
    public function testHandlerToAllowAllowDomains(): void
    {
        config([
            'cors.access-control-allow-origin' => '*',
            'cors.access-control-allow-methods' => 'a',
            'cors.access-control-allow-credentials' => 'a',
            'cors.access-control-max-age' => 'a',
            'cors.access-control-allow-headers' => 'a',
        ]);

        try {
            $response = $this->call('get', '/cors-middleware');

            $responseHeaders = $response->headers->all();

            $this->assertTrue(
                array_key_exists('access-control-allow-origin', $responseHeaders),
                "Response haven't the expected header: access-control-allow-origin"
            );

            $headerValue = $responseHeaders['access-control-allow-origin'][0];
            $this->assertEquals(
                '*',
                $headerValue,
                "Response header value is not correct: {$headerValue}"
            );

            $this->assertTrue(true, "Middleware is working to allow all domains!");
        } catch (Exception $exception) {
            $this->assertTrue(false, "Middleware is not working to allow all domains!");
        }
    }

    public function testShouldAssertIlluminateResponse ()
    {
        config([
            'cors.access-control-allow-origin' => 'http://myrandomdomain',
            'cors.access-control-allow-methods' => 'a',
            'cors.access-control-allow-credentials' => 'a',
            'cors.access-control-max-age' => 'a',
            'cors.access-control-allow-headers' => 'a',
        ]);

        $response = $this->call('post', '/cors-middleware',[
            new IlluminateResponse()
        ]);

        $responseHeaders = $response->headers->all();

        $this->assertTrue(
            array_key_exists('access-control-allow-origin', $responseHeaders),
            "Response haven't the expected header: access-control-allow-origin"
        );
    }

    public function testShouldAssertSymfonyResponse ()
    {
        config([
            'cors.access-control-allow-origin' => 'http://myrandomdomain',
            'cors.access-control-allow-methods' => 'a',
            'cors.access-control-allow-credentials' => 'a',
            'cors.access-control-max-age' => 'a',
            'cors.access-control-allow-headers' => 'a',
        ]);

        $response = $this->call('post', '/cors-middleware',[
            new SymfonyResponse()
        ]);

        $responseHeaders = $response->headers->all();

        $this->assertTrue(
            array_key_exists('access-control-allow-origin', $responseHeaders),
            "Response haven't the expected header: access-control-allow-origin"
        );
    }
}
