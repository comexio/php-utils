<?php

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use Logcomex\PhpUtils\Contracts\MockContract;
use Logcomex\PhpUtils\Enumerators\ErrorEnum;
use Logcomex\PhpUtils\Exceptions\BadImplementationException;
use Logcomex\PhpUtils\Helpers\HttpHelper;
use Logcomex\PhpUtils\Singletons\TracerSingleton;

/**
 * Class HttpHelperUnitTest
 */
class HttpHelperUnitTest extends TestCase
{
    /**
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        TracerSingleton::setTraceValue('');
    }

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
    public function testIsTestMode_TrueFlow(): void
    {
        config([
            'app.mode' => 'test',
        ]);
        $httpHelper = new HttpHelper();
        $response = $httpHelper->isTestMode();
        $this->assertTrue($response);
    }

    /**
     * @return void
     */
    public function testIsTestMode_FalseFlow(): void
    {
        config([
            'app.mode' => 'prod',
        ]);
        $httpHelper = new HttpHelper();
        $response = $httpHelper->isTestMode();
        $this->assertIsBool($response);
        $this->assertFalse($response);
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
    public function test__call_WithNotMockedEndpointAndOutTestMode_SuccessFlow(): void
    {
        config(['app.mode' => 'prod',]);
        $httpHelper = new HttpHelper();

        // If an error occurs, it means that the guzzle is not mocking
        $this->expectException(Exception::class);
        $httpHelper->post('api/not/mocked');
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

        $expectedException = new BadImplementationException(
            ErrorEnum::PHU004,
            'Mock Class registered does not exist.'
        );
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
            ErrorEnum::PHU003,
            'You are requesting to external APIs in test mode. Please mock your endpoint.'
        );

        $this->expectCustomException($expectedException, function () {
            $httpHelper = new HttpHelper();
            $httpHelper->post('api/not/mocked');
        });
    }

    /**
     * @return void
     */
    public function test__call_WithTracerWithoutHeaderBundle_SuccessFlow(): void
    {
        config(['tracer.headersToPropagate' => ['x-tracer-id',],]);
        TracerSingleton::setTraceValue('test');

        $httpHelper = new HttpHelper();
        $response = $httpHelper->post('api/mocked', [RequestOptions::DEBUG => false,]);

        $this->assertNotNull($response);
    }

    /**
     * @return void
     */
    public function test__call_WithTracerWithHeaderBundle_SuccessFlow(): void
    {
        config(['tracer.headersToPropagate' => ['x-tracer-id',],]);
        TracerSingleton::setTraceValue('test');

        $httpHelper = new HttpHelper();
        $response = $httpHelper->post('api/mocked', [
            RequestOptions::HEADERS => ['test-header-key' => 'test',]
        ]);

        $this->assertNotNull($response);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEmpty($response->getBody()->getContents());
    }

    /**
     * @return void
     */
    public function test__call_WithTracerWithoutOptions_SuccessFlow(): void
    {
        config(['tracer.headersToPropagate' => ['x-tracer-id',],]);
        TracerSingleton::setTraceValue('test');

        $httpHelper = new HttpHelper();
        $response = $httpHelper->post('api/mocked');

        $this->assertNotNull($response);
    }

    /**
     * @return void
     * @throws BadImplementationException
     */
    public function testPropagateTracerValue_HappyPath_WithoutHeaderBundle_SuccessFlow(): void
    {
        $headerNameToPropagate = 'x-tracer-id';
        config(['tracer.headersToPropagate' => [$headerNameToPropagate,],]);
        $tracerValue = 'test';
        $functionArguments = ['mocked/endpoint', [RequestOptions::DEBUG => false,]];
        $response = HttpHelper::propagateTracerValue($tracerValue, $functionArguments);

        $this->assertIsArray($response);
        $this->assertCount(count($functionArguments), $response);

        $supposedRequestOptions = $response[1];
        $this->assertIsArray($supposedRequestOptions);

        $this->assertArrayHasKey(RequestOptions::DEBUG, $supposedRequestOptions);
        $this->assertFalse($supposedRequestOptions[RequestOptions::DEBUG]);

        $this->assertArrayHasKey(RequestOptions::HEADERS, $supposedRequestOptions);
        $this->assertIsArray($supposedRequestOptions[RequestOptions::HEADERS]);

        $this->assertArrayHasKey($headerNameToPropagate, $supposedRequestOptions[RequestOptions::HEADERS]);
        $this->assertEquals($tracerValue, $supposedRequestOptions[RequestOptions::HEADERS][$headerNameToPropagate]);
    }

    /**
     * @return void
     * @throws BadImplementationException
     */
    public function testPropagateTracerValue_HappyPath_WithHeaderBundle_SuccessFlow(): void
    {
        $headerNameToPropagate = 'x-tracer-id';
        config(['tracer.headersToPropagate' => [$headerNameToPropagate,],]);
        $tracerValue = 'test';
        $functionArguments = [
            'mocked/endpoint', [
                RequestOptions::DEBUG => false,
                RequestOptions::HEADERS => ['test' => 'test',],
            ],
        ];
        $response = HttpHelper::propagateTracerValue($tracerValue, $functionArguments);

        $this->assertIsArray($response);
        $this->assertCount(count($functionArguments), $response);

        $supposedRequestOptions = $response[1];
        $this->assertIsArray($supposedRequestOptions);

        $this->assertArrayHasKey(RequestOptions::DEBUG, $supposedRequestOptions);
        $this->assertFalse($supposedRequestOptions[RequestOptions::DEBUG]);

        $this->assertArrayHasKey(RequestOptions::HEADERS, $supposedRequestOptions);
        $this->assertIsArray($supposedRequestOptions[RequestOptions::HEADERS]);

        $this->assertArrayHasKey($headerNameToPropagate, $supposedRequestOptions[RequestOptions::HEADERS]);
        $this->assertEquals($tracerValue, $supposedRequestOptions[RequestOptions::HEADERS][$headerNameToPropagate]);
    }

    /**
     * @return void
     * @throws BadImplementationException
     */
    public function testPropagateTracerValue_MultipleHeadersNameToPropagate_SuccessFlow(): void
    {
        $headersNamesToPropagate = ['x-tracer-id', 'x-tracer-id-2',];
        config(['tracer.headersToPropagate' => $headersNamesToPropagate,]);
        $tracerValue = 'test';
        $functionArguments = [
            'mocked/endpoint', [
                RequestOptions::DEBUG => false,
                RequestOptions::HEADERS => ['test' => 'test',],
            ],
        ];
        $response = HttpHelper::propagateTracerValue($tracerValue, $functionArguments);

        $this->assertIsArray($response);
        $this->assertCount(count($functionArguments), $response);

        $supposedRequestOptions = $response[1];
        $this->assertIsArray($supposedRequestOptions);

        $this->assertArrayHasKey(RequestOptions::DEBUG, $supposedRequestOptions);
        $this->assertFalse($supposedRequestOptions[RequestOptions::DEBUG]);

        $this->assertArrayHasKey(RequestOptions::HEADERS, $supposedRequestOptions);
        $this->assertIsArray($supposedRequestOptions[RequestOptions::HEADERS]);

        $this->assertArrayHasKey($headersNamesToPropagate[0], $supposedRequestOptions[RequestOptions::HEADERS]);
        $this->assertArrayHasKey($headersNamesToPropagate[1], $supposedRequestOptions[RequestOptions::HEADERS]);

        foreach ($headersNamesToPropagate as $headerName) {
            $this->assertEquals($tracerValue, $supposedRequestOptions[RequestOptions::HEADERS][$headerName]);
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testPropagateTracerValue_WithSettingsNotArray_SuccessFlow(): void
    {
        $headerNameToPropagate = 'x-tracer-id';
        config(['tracer.headersToPropagate' => $headerNameToPropagate,]);
        $tracerValue = 'test';
        $functionArguments = [
            'mocked/endpoint', [
                RequestOptions::DEBUG => false,
                RequestOptions::HEADERS => ['test' => 'test',],
            ],
        ];
        $response = HttpHelper::propagateTracerValue($tracerValue, $functionArguments);

        $this->assertIsArray($response);
        $this->assertCount(count($functionArguments), $response);

        $supposedRequestOptions = $response[1];
        $this->assertIsArray($supposedRequestOptions);

        $this->assertArrayHasKey(RequestOptions::DEBUG, $supposedRequestOptions);
        $this->assertFalse($supposedRequestOptions[RequestOptions::DEBUG]);

        $this->assertArrayHasKey(RequestOptions::HEADERS, $supposedRequestOptions);
        $this->assertIsArray($supposedRequestOptions[RequestOptions::HEADERS]);

        $this->assertArrayHasKey($headerNameToPropagate, $supposedRequestOptions[RequestOptions::HEADERS]);
        $this->assertEquals($tracerValue, $supposedRequestOptions[RequestOptions::HEADERS][$headerNameToPropagate]);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testCallWithMockedEndpointCustomHttpErrorFlow(): void
    {
        config([
            'app.mode' => 'test',
            'mockedEndpoints.api/mocked' => ApiTestMock::class,
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionCode(500);

        HttpHelper::mustReturnError(500);
        try {
            $httpHelper = new HttpHelper();
            $httpHelper->post('api/mocked');
        } finally{
            HttpHelper::mustNotReturnError();
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
