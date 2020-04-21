<?php

/**
 * Class CorsMiddlewareUnitTest
 */
class CorsMiddlewareUnitTest extends TestCase
{
    public function testHandler(): void
    {
        config([
            'cors.access-control-allow-origin' => 'http://localhost:80',
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
                    'access-control-allow-origin' => 'http://localhost:80',
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
}
