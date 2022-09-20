<?php

namespace Tests\Middlewares;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Logcomex\PhpUtils\Middlewares\AuthenticateMiddleware;
use Tests\TestCase;

/**
 * Class AuthenticateMiddlewareUnitTest
 */
class AuthenticateMiddlewareUnitTest extends TestCase
{
    /**
     * @param Closure $handler
     */
    private function bootAuthProvider(Closure $handler): void
    {
        $this->app['auth']->viaRequest('api', $handler);
    }

    /**
     * @return void
     * @throws AuthenticationException
     */
    public function testHandle_UnauthorizedFlow(): void
    {
        $this->bootAuthProvider(function (Request $request) {
            if (!$request->hasHeader('authorization') || !$request->header('user-email')) {
                return;
            }
        });

        $expectedException = new AuthenticationException('Unauthorized');
        $this->expectExceptionObject($expectedException);

        $fakeRequest = new Request();
        $middleware = new AuthenticateMiddleware($this->app['auth']);
        $middleware->handle($fakeRequest, function () {});
    }

    /**
     * @return void
     * @throws AuthenticationException
     */
    public function testHandle_AuthorizedFlow(): void
    {
        $this->bootAuthProvider(function (Request $request) {
            return isset($request);
        });

        $fakeRequest = new Request();
        $middleware = new AuthenticateMiddleware($this->app['auth']);
        $response = $middleware->handle($fakeRequest, function () { return response()->json(); });

        $this->assertInstanceOf(JsonResponse::class, $response);
    }
}
