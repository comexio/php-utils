<?php

namespace Logcomex\PhpUtils\Handlers;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Support\Responsable;
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
    public static function exportExceptionToArray(Throwable $throwable): array
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
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Exception $e
     * @return void
     */
    public function report(Exception|Throwable $e): void
    {
        $treatedException = self::exportExceptionToArray($e);
        $traceId = TracerSingleton::getTraceValue();

        Log::error("[[REQUEST_ERROR]] | {$traceId} |", $treatedException);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Exception $e
     * @return \Illuminate\Http\Response|JsonResponse
     */
    public function render($request, Exception|Throwable $e): JsonResponse
    {
        switch (true) {
            case $e instanceof AuthenticationException:
                return response()
                    ->json([
                        'trace' => TracerSingleton::getTraceValue(),
                        'message' => 'Não autenticado',
                        'error' => 'Unauthorized'
                    ], Response::HTTP_UNAUTHORIZED);
            case $e instanceof QueryException:
                return response()
                    ->json([
                        'trace' => TracerSingleton::getTraceValue(),
                        'message' => 'Tivemos um problema com nosso banco de dados',
                        'error' => 'Bad Gateway'
                    ], Response::HTTP_BAD_GATEWAY);
            case $e instanceof NotFoundHttpException:
                return response()
                    ->json([
                        'trace' => TracerSingleton::getTraceValue(),
                        'message' => 'A página solicitada não pôde ser encontrada',
                        'error' => 'Page not found'
                    ], Response::HTTP_NOT_FOUND);
            case $e instanceof ValidationException:
                return response()
                    ->json([
                        'trace' => TracerSingleton::getTraceValue(),
                        'message' => __('common.errors.data_invalid'),
                        'error' => $e->validator->errors()
                    ], Response::HTTP_NOT_ACCEPTABLE);
            case $e instanceof MethodNotAllowedHttpException:
                return response()
                    ->json([
                        'trace' => TracerSingleton::getTraceValue(),
                        'message' => 'O método para essa requisição está incorreto.',
                        'error' => 'Method Not Allowed'
                    ], Response::HTTP_METHOD_NOT_ALLOWED);
            case $e instanceof ApiException:
                return response()
                    ->json([
                        'trace' => TracerSingleton::getTraceValue(),
                        'message' => $e->getMessage(),
                        'code' => $e->getToken()
                    ], $e->getHttpCode());
            case $e instanceof BadImplementationException:
                return response()
                    ->json([
                        'trace' => TracerSingleton::getTraceValue(),
                        'code' => $e->getToken(),
                        'message' => 'Tivemos um erro na aplicação!'
                    ], $e->getHttpCode());
            case $e instanceof SecurityException:
                return response()
                    ->json([
                        'trace' => TracerSingleton::getTraceValue(),
                        'code' => $e->getToken(),
                        'message' => 'Tivemos um erro de segurança na aplicação!',
                        'reason' => $e->getMessage(),
                    ], $e->getHttpCode());
            case $e instanceof UnavailableServiceException:
                return response()
                    ->json([
                        'trace' => TracerSingleton::getTraceValue(),
                        'code' => $e->getToken(),
                        'message' => "Service {$e->getService()} apresenta problemas!",
                        'service' => $e->getService(),
                        'reason' => $e->getMessage(),
                    ], $e->getHttpCode());
            case $e instanceof TooManyRequestsHttpException:
                return response()
                    ->json([
                        'trace' => TracerSingleton::getTraceValue(),
                        'message' => 'Limite de requisição excedido.',
                        'error' => 'Too Many Requests.'
                    ], Response::HTTP_TOO_MANY_REQUESTS);
            default:

                if (method_exists($e, 'render')) {
                    return $e->render($request);
                } elseif ($e instanceof Responsable) {
                    return $e->toResponse($request);
                }

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
