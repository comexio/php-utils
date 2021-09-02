<?php

namespace Logcomex\PhpUtils\Handlers;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler;
use Logcomex\PhpUtils\Exceptions\ApiException;
use Logcomex\PhpUtils\Exceptions\BadImplementationException;
use Logcomex\PhpUtils\Exceptions\SecurityException;
use Logcomex\PhpUtils\Exceptions\UnavailableServiceException;
use Logcomex\PhpUtils\Singletons\TracerSingleton;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;

/**
 * Class ExceptionHandler
 * @package Logcomex\PhpUtils\Handlers
 */
class ExceptionHandler extends Handler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * @param Throwable $throwable
     * @return array
     */
    public static function exportThrowableToArray(Throwable $throwable): array
    {
        if (method_exists($throwable, 'toArray')) {
            return $throwable->toArray();
        }

        return [
            'exception-class' => class_basename($throwable),
            'message' => $throwable->getMessage(),
            'file' => $throwable->getFile(),
            'line' => $throwable->getLine(),
            'code' => $throwable->getCode(),
        ];
    }

    /**
     * Report or log a throwable.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Throwable $throwable
     * @return void
     */
    public function report(Throwable $throwable): void
    {
        $treatedException = self::exportThrowableToArray($throwable);
        $traceId = TracerSingleton::getTraceValue();

        Log::error("[[REQUEST_ERROR]] | {$traceId} |", $treatedException);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Exception $exception
     * @return \Illuminate\Http\Response|JsonResponse
     */
    public function render($request, Exception $exception): JsonResponse
    {
        switch (true) {
            case $exception instanceof AuthenticationException:
                return response()
                    ->json([
                        'trace' => TracerSingleton::getTraceValue(),
                        'message' => 'Não autenticado',
                        'error' => 'Unauthorized'
                    ], Response::HTTP_UNAUTHORIZED);
            case $exception instanceof QueryException:
                return response()
                    ->json([
                        'trace' => TracerSingleton::getTraceValue(),
                        'message' => 'Tivemos um problema com nosso banco de dados',
                        'error' => 'Bad Gateway'
                    ], Response::HTTP_BAD_GATEWAY);
            case $exception instanceof NotFoundHttpException:
                return response()
                    ->json([
                        'trace' => TracerSingleton::getTraceValue(),
                        'message' => 'A página solicitada não pôde ser encontrada',
                        'error' => 'Page not found'
                    ], Response::HTTP_NOT_FOUND);
            case $exception instanceof ValidationException:
                return response()
                    ->json([
                        'trace' => TracerSingleton::getTraceValue(),
                        'message' => __('common.errors.data_invalid'),
                        'error' => $exception->validator->errors()
                    ], Response::HTTP_NOT_ACCEPTABLE);
            case $exception instanceof MethodNotAllowedHttpException:
                return response()
                    ->json([
                        'trace' => TracerSingleton::getTraceValue(),
                        'message' => 'O método para essa requisição está incorreto.',
                        'error' => 'Method Not Allowed'
                    ], Response::HTTP_METHOD_NOT_ALLOWED);
            case $exception instanceof ApiException:
                return response()
                    ->json([
                        'trace' => TracerSingleton::getTraceValue(),
                        'message' => $exception->getMessage(),
                        'code' => $exception->getToken()
                    ], $exception->getHttpCode());
            case $exception instanceof BadImplementationException:
                return response()
                    ->json([
                        'trace' => TracerSingleton::getTraceValue(),
                        'code' => $exception->getToken(),
                        'message' => 'Tivemos um erro na aplicação!'
                    ], $exception->getHttpCode());
            case $exception instanceof SecurityException:
                return response()
                    ->json([
                        'trace' => TracerSingleton::getTraceValue(),
                        'code' => $exception->getToken(),
                        'message' => 'Tivemos um erro de segurança na aplicação!',
                        'reason' => $exception->getMessage(),
                    ], $exception->getHttpCode());
            case $exception instanceof UnavailableServiceException:
                return response()
                    ->json([
                        'trace' => TracerSingleton::getTraceValue(),
                        'code' => $exception->getToken(),
                        'message' => "Service {$exception->getService()} apresenta problemas!",
                        'service' => $exception->getService(),
                        'reason' => $exception->getMessage(),
                    ], $exception->getHttpCode());
            case $exception instanceof TooManyRequestsHttpException:
                return response()
                    ->json([
                        'trace' => TracerSingleton::getTraceValue(),
                        'message' => 'Limite de requisição excedido.',
                        'error' => 'Too Many Requests.'
                    ], Response::HTTP_TOO_MANY_REQUESTS);
            default:
                return response()
                    ->json([
                        'trace' => TracerSingleton::getTraceValue(),
                        'message' => 'Tivemos um erro inesperado.',
                        'error' => 'Internal server error',
                        'request' => $request->all(),
                    ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
