<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Logcomex\PhpUtils\Exceptions\SecurityException;
use Logcomex\PhpUtils\Middlewares\AccreditedApiKeysMiddleware;

/**
 * Class AccreditedApiKeysMiddlewareUnitTest
 */
class AccreditedApiKeysMiddlewareUnitTest extends TestCase
{
    /**
     * @return void
     * @throws SecurityException
     */
    public function testValidateXApiKeyDataSuccessFlow(): void
    {
        $middleware = new AccreditedApiKeysMiddleware();

        $response = $middleware->validateXApiKeyData(['test' => 'test'], 'test');

        $this->assertNull($response);
    }

    /**
     * @throws Exception
     */
    public function testValidateXApiKeyData_ApiKeyInvalid(): void
    {
        $middleware = new AccreditedApiKeysMiddleware();

        $expectedException = new SecurityException(
            'SEC04',
            'Your x-api-key header is invalid.',
            401
        );
        $this->expectCustomException(
            $expectedException,
            function () use ($middleware) {
                $middleware->validateXApiKeyData(['test' => 'test'], '54654');
            }
        );
    }

    /**
     * @throws Exception
     */
    public function testValidateXApiKeyData_ApiKeyNotInformed(): void
    {
        $middleware = new AccreditedApiKeysMiddleware();

        $expectedException = new SecurityException(
            'SEC03',
            'This endpoint is protected by ApiKey, you must provide a valid x-api-key header to use.',
            401
        );
        $this->expectCustomException(
            $expectedException,
            function () use ($middleware) {
                $middleware->validateXApiKeyData(['test' => 'test'], '');
            }
        );
    }

    /**
     * @return void
     */
    public function testSetWelcomeHeaderInResponse_KnownApi(): void
    {
        $middleware = new AccreditedApiKeysMiddleware();

        $fakeResponse = new Response();

        config(['app.api-name' => 'test']);
        $response = $middleware->setWelcomeHeaderInResponse(
            $fakeResponse,
            ['test' => '623187s8712yw8'],
            '623187s8712yw8'
        );

        $this->assertInstanceOf(Response::class, $response);
        $this->assertTrue($response->headers->has('Welcome-Message'));
        $this->assertEquals(
            'Welcome to the test. You are using the x-api-key credential: test',
            $response->headers->get('Welcome-Message')
        );
    }

    /**
     * @return void
     */
    public function testSetWelcomeHeaderInResponse_UnknownApi(): void
    {
        $middleware = new AccreditedApiKeysMiddleware();

        $fakeResponse = new Response();

        $response = $middleware->setWelcomeHeaderInResponse(
            $fakeResponse,
            ['test' => '623187s8712yw8'],
            '623187s8712yw8'
        );

        $this->assertInstanceOf(Response::class, $response);
        $this->assertTrue($response->headers->has('Welcome-Message'));
        $this->assertEquals(
            'Welcome to the UnknowApi. You are using the x-api-key credential: test',
            $response->headers->get('Welcome-Message')
        );
    }

    /**
     * @return void
     * @throws SecurityException
     */
    public function testHandler_SuccessFlow(): void
    {
        $middleware = new AccreditedApiKeysMiddleware();

        config(['accreditedApiKeys' => [
            'source-test-api' => '1721wt712w6216t'
        ]]);
        $fakeRequest = new Request();
        $fakeRequest->headers->set('x-api-key', '1721wt712w6216t');

        $response = $middleware->handle($fakeRequest, function () {
            return response()->json('test');
        });

        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testHandler_FailureFlowNotSendingHeader(): void
    {
        config(['accreditedApiKeys' => [
            'source-test-api' => '1721wt712w6216t'
        ]]);
        $expectedException = new SecurityException(
            'SEC03',
            'This endpoint is protected by ApiKey, you must provide a valid x-api-key header to use.',
            401
        );

        $this->expectCustomException($expectedException, function () {
            $middleware = new AccreditedApiKeysMiddleware();

            $fakeRequest = new Request();
            $fakeRequest->headers->set('x-api-keyy', '');

            $middleware->handle($fakeRequest, function () {
                return response()->json('test');
            });
        });
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testHandler_FailureFlowSendingHeader(): void
    {
        config(['accreditedApiKeys' => [
            'source-test-api' => '1721wt712w6216t'
        ]]);
        $expectedException = new SecurityException(
            'SEC04',
            'Your x-api-key header is invalid.',
            401
        );

        $this->expectCustomException($expectedException, function () {
            $middleware = new AccreditedApiKeysMiddleware();

            $fakeRequest = new Request();
            $fakeRequest->headers->set('x-api-key', 'invalid');

            $middleware->handle($fakeRequest, function () {
                return response()->json('test');
            });
        });
    }
}
