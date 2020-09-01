<?php

use GuzzleHttp\Psr7\Response;
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
    public function testIsMockedEndpoint_TrueFlow(): void
    {
        config([
            'mockedEndpoints.api/test' => ApiTestMock::class,
        ]);
        $httpHelper = new HttpHelper();

        $response = $httpHelper->isMockedEndpoint('api/test');

        $this->assertIsBool($response);
        $this->assertTrue($response);
    }

    /**
     * @return void
     */
    public function testIsMockedEndpoint_FalseFlow(): void
    {
        $httpHelper = new HttpHelper();

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

        $response = $httpHelper->post('api/mocked');
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test__callWithMockedEndpoint_FakeFailureFlow(): void
    {
        config([
            'app.mode' => 'test',
            'mockedEndpoints.api/mocked' => ApiTestMock::class,
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionCode(400);

        HttpHelper::mustReturnError();
        try {
            $httpHelper = new HttpHelper();
            $httpHelper->post('api/mocked');
        } finally{
            HttpHelper::mustNotReturnError();
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test__callWithMockedEndpointAndNotExistingMockClass(): void
    {
        config([
            'app.mode' => 'test',
            'mockedEndpoints.api/mocked' => 'ClassDoesNotExists',
        ]);

        $expectedException = new BadImplementationException('PHU-004', 'Mock Class registered does not exists.');
        $this->expectCustomException($expectedException, function () {
            $httpHelper = new HttpHelper();
            $httpHelper->post('api/mocked');
        });
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test__callWithNotMockedEndpointInTestMode(): void
    {
        config(['app.mode' => 'test']);
        $expectedException = new BadImplementationException(
            'PHU-003',
            'You are requesting to external APIs in test mode. Please mock your endpoint.'
        );

        $this->expectCustomException($expectedException, function () {
            $httpHelper = new HttpHelper();
            $httpHelper->post('api/not/mocked');
        });
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
