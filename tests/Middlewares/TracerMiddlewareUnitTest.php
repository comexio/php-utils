<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Logcomex\PhpUtils\Exceptions\BadImplementationException;
use Logcomex\PhpUtils\Helpers\TokenHelper;
use Logcomex\PhpUtils\Middlewares\TracerMiddleware;
use Logcomex\PhpUtils\Singletons\TracerSingleton;

/**
 * Class TracerMiddlewareUnitTest
 */
class TracerMiddlewareUnitTest extends TestCase
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
     * @throws BadImplementationException
     */
    public function testHandler_HappyPath_SuccessFlow(): void
    {
        config([
            'tracer.headersToPropagate' => ['x-trace-id',],
        ]);

        $middleware = new TracerMiddleware();

        $expectedTraceId = TokenHelper::generate(false);

        $fakeRequest = new Request();
        $fakeRequest->headers->set('x-trace-id', $expectedTraceId);

        $response = $middleware->handle($fakeRequest, function () {
            return response()->json();
        });

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertIsString(TracerSingleton::getTraceValue());
        $this->assertEquals($expectedTraceId, TracerSingleton::getTraceValue());
    }

    /**
     * @return void
     * @throws BadImplementationException
     */
    public function testHandler_TwoHeaderOptionsSentInRequest_SuccessFlow(): void
    {
        config([
            'tracer.headersToPropagate' => ['x-trace-id', 'x-trace-id-2',],
        ]);

        $middleware = new TracerMiddleware();

        $expectedTraceId1 = TokenHelper::generate(false);
        $expectedTraceId2 = TokenHelper::generate(false);

        $fakeRequest = new Request();
        $fakeRequest->headers->set('x-trace-id', $expectedTraceId1);
        $fakeRequest->headers->set('x-trace-id-2', $expectedTraceId2);

        $response = $middleware->handle($fakeRequest, function () {
            return response()->json();
        });

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertIsString(TracerSingleton::getTraceValue());
        $this->assertEquals($expectedTraceId1, TracerSingleton::getTraceValue());
    }

    /**
     * @return void
     * @throws BadImplementationException
     */
    public function testHandler_WithHeaderThatIsNotToBeTraced_SuccessFlow(): void
    {
        config([
            'tracer.headersToPropagate' => ['x-trace-id'],
        ]);

        $middleware = new TracerMiddleware();

        $expectedTraceId1 = TokenHelper::generate(false);
        $expectedTraceId2 = TokenHelper::generate(false);

        $fakeRequest = new Request();
        $fakeRequest->headers->set('x-trace-id', $expectedTraceId1);
        $fakeRequest->headers->set('x-trace-id-2', $expectedTraceId2);

        $response = $middleware->handle($fakeRequest, function () {
            return response()->json();
        });

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertIsString(TracerSingleton::getTraceValue());
        $this->assertEquals($expectedTraceId1, TracerSingleton::getTraceValue());
    }

    /**
     * @return void
     * @throws BadImplementationException
     */
    public function testHandler_WithoutHeader_SuccessFlow(): void
    {
        config([
            'tracer.headersToPropagate' => ['x-trace-id'],
        ]);

        $middleware = new TracerMiddleware();

        $fakeRequest = new Request();

        $response = $middleware->handle($fakeRequest, function () {
            return response()->json();
        });

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertIsString(TracerSingleton::getTraceValue());
        $this->assertNotEmpty(TracerSingleton::getTraceValue());
    }

    /**
     * @return void
     * @throws BadImplementationException
     */
    public function testHandler_WithNotArraySetting_SuccessFlow(): void
    {
        config([
            'tracer.headersToPropagate' => 'x-trace-id',
        ]);

        $middleware = new TracerMiddleware();

        $fakeRequest = new Request();

        $response = $middleware->handle($fakeRequest, function () {
            return response()->json();
        });

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertIsString(TracerSingleton::getTraceValue());
        $this->assertNotEmpty(TracerSingleton::getTraceValue());
    }

    /**
     * @return void
     * @throws BadImplementationException
     */
    public function testHandler_InsensitiveCase_SuccessFlow(): void
    {
        config([
            'tracer.headersToPropagate' => 'X-Trace-Id',
        ]);

        $middleware = new TracerMiddleware();

        $fakeRequest = new Request();
        $fakeRequest->headers->set('x-trace-id', 'test');

        $response = $middleware->handle($fakeRequest, function () {
            return response()->json();
        });

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertIsString(TracerSingleton::getTraceValue());
        $this->assertNotEmpty(TracerSingleton::getTraceValue());
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testHandler_MissingTraceSettings_FailureFlow(): void
    {
        $expectedException = new BadImplementationException(
            'PHU-005',
            'You must provide at least one header name to trace.'
        );
        $this->expectCustomException($expectedException, function () {
            $middleware = new TracerMiddleware();
            $fakeRequest = new Request();

            $middleware->handle($fakeRequest, function () {
                return response()->json();
            });
        });
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testHandler_EmptyTraceSettings_FailureFlow(): void
    {
        config(['tracer.headersToPropagate' => []]);
        $expectedException = new BadImplementationException(
            'PHU-005',
            'You must provide at least one header name to trace.'
        );
        $this->expectCustomException($expectedException, function () {
            $middleware = new TracerMiddleware();
            $fakeRequest = new Request();

            $middleware->handle($fakeRequest, function () {
                return response()->json();
            });
        });
    }
}
