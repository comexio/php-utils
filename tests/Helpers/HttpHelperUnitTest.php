<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Logcomex\PhpUtils\Contracts\MockContract;
use Logcomex\PhpUtils\Exceptions\BadImplementationException;
use Logcomex\PhpUtils\Helpers\HttpHelper;

/**
 * Class HttpHelperUnitTest
 */
class HttpHelperUnitTest extends TestCase
{
    /**
     * @return void
     */
    public function test__construct(): void
    {
        $httpHelper = new HttpHelper();

        $response = $httpHelper->__construct();
        $this->assertNull($response);
    }

    /**
     * @return void
     */
    public function testIsTestMode(): void
    {
        config([
            'app.mode' => 'prod',
        ]);
        $httpHelper = new HttpHelper();
        $response = $httpHelper->isTestMode();
        $this->assertIsBool($response);
        $this->assertFalse($response);

        config([
            'app.mode' => 'test',
        ]);
        $response = $httpHelper->isTestMode();
        $this->assertTrue($response);
    }

    /**
     * @return void
     */
    public function testIsMockedEndpoint(): void
    {
        config([
            'mockedEndpoints.api/test' => ApiTestMock::class,
        ]);
        $httpHelper = new HttpHelper();

        $response = $httpHelper->isMockedEndpoint('api/test');
        $this->assertIsBool($response);
        $this->assertTrue($response);

        $response = $httpHelper->isMockedEndpoint('api/not/mocked');
        $this->assertIsBool($response);
        $this->assertFalse($response);
    }

    /**
     * @return void
     */
    public function test__callWithNotMockedEndpoint(): void
    {
        $httpHelper = new HttpHelper();

        try {
            $httpHelper->post('api/not/mocked');
            $this->assertTrue(false);
        } catch (Exception $exception) {
            // If an error occurs, it means that the guzzle is not mocking
            $this->assertTrue(true);
        }
    }

    /**
     * @return void
     */
    public function test__callWithMockedEndpoint(): void
    {
        config([
            'app.mode' => 'test',
            'mockedEndpoints.api/mocked' => ApiTestMock::class,
        ]);
        $httpHelper = new HttpHelper();

        try {
            $response = $httpHelper->post('api/mocked');
            $this->assertInstanceOf(\GuzzleHttp\Psr7\Response::class, $response);
            $this->assertEquals(200, $response->getStatusCode());
        } catch (Exception $exception) {
            // If an error occurs, it means that the guzzle is not mocking
            $this->assertTrue(false);
        }

        HttpHelper::mustReturnError();
        try {
            $httpHelper->post('api/mocked');
            // If an error not occurs, it means that the guzzle is not mocking
            $this->assertTrue(false);
        } catch (Exception $exception) {
            $this->assertEquals(400, $exception->getCode());
        } finally{
            HttpHelper::mustNotReturnError();
        }

        config([
            'app.mode' => 'test',
            'mockedEndpoints.api/mocked' => 'ClassDoesNotExists',
        ]);
        $httpHelper = new HttpHelper();

        try {
            $httpHelper->post('api/mocked');
            $this->assertTrue(false, 'Validation of class existence its not working');
        } catch (Exception $exception) {
            $this->assertInstanceOf(BadImplementationException::class, $exception);
            $this->assertEquals('Mock Class registered does not exists.', $exception->getMessage());
        }
    }
}

/**
 * Class ApiTestMock
 */
class ApiTestMock implements MockContract
{
    /**
     * @return string
     */
    public static function mock(): string
    {
        return '';
    }
}
