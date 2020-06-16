<?php

namespace Logcomex\PhpUtils\Handlers;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler;
use Logcomex\PhpUtils\Exceptions\ApiException;
use Logcomex\PhpUtils\Exceptions\BadImplementationException;
use Logcomex\PhpUtils\Exceptions\SecurityException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
     * @param Exception $exception
     * @return array
     */
    public function exportExceptionToArray(Exception $exception): array
    {
        if (method_exists($exception, 'toArray')) {
            return $exception->toArray();
        }

        return [
            'exception-class' => class_basename($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'code' => $exception->getCode(),
        ];
    }

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Exception $exception
     * @return void
     */
    public function report(Exception $exception): void
    {
        $treatedException = $this->exportExceptionToArray($exception);

        Log::error('Ocorreu um erro!', $treatedException);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param Exception $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function render($request, Exception $exception): JsonResponse
    {
        switch (true) {
            case $exception instanceof AuthenticationException:
                return response()
                    ->json([
                        'message' => 'Não autenticado',
                        'error' => 'Unauthorized'
                    ], Response::HTTP_UNAUTHORIZED);
            case $exception instanceof QueryException:
                return response()
                    ->json([
                        'message' => 'Tivemos um problema com nosso banco de dados',
                        'error' => 'Bad Gateway'
                    ], Response::HTTP_BAD_GATEWAY);
            case $exception instanceof NotFoundHttpException:
                return response()
                    ->json([
                        'message' => 'A página solicitada não pôde ser encontrada',
                        'error' => 'Page not found'
                    ], Response::HTTP_NOT_FOUND);
            case $exception instanceof ValidationException:
                return response()
                    ->json([
                        'message' => __('common.errors.data_invalid'),
                        'error' => $exception->validator->errors()
                    ], Response::HTTP_NOT_ACCEPTABLE);
            case $exception instanceof MethodNotAllowedHttpException:
                return response()
                    ->json([
                        'message' => 'O método para essa requisição está incorreto.',
                        'error' => 'Method Not Allowed'
                    ], Response::HTTP_METHOD_NOT_ALLOWED);
            case $exception instanceof ApiException:
                return response()
                    ->json(
                        ['message' => $exception->getMessage(), 'code' => $exception->getToken()],
                        $exception->getHttpCode()
                    );
            case $exception instanceof BadImplementationException:
                return response()
                    ->json(['message' => 'Tivemos um erro na aplicação!'], $exception->getHttpCode());
            case $exception instanceof SecurityException:
                return response()
                    ->json([
                        'code' => $exception->getToken(),
                        'message' => 'Tivemos um erro de segurança na aplicação!',
                        'reason' => $exception->getMessage(),
                    ], $exception->getHttpCode());
            default:
                return response()
                    ->json([
                        'message' => 'Tivemos um erro inesperado.',
                        'error' => 'Internal server error',
                        'request' => $request->all(),
                    ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
