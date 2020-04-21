<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Logcomex\PhpUtils\Exceptions\BadImplementationException;
use PHPUnit\Framework\TestCase;
use Logcomex\PhpUtils\Handlers\ExceptionHandler;
use Logcomex\PhpUtils\Exceptions\ApiException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\Validator;

/**
 * Class ExceptionHandlerUnitTest
 */
class ExceptionHandlerUnitTest extends TestCase
{
    /**
     * @var ExceptionHandler
     */
    private $handler;

    /**
     * ExceptionHandlerUnitTest constructor.
     * @param null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->handler = new ExceptionHandler();
    }

    /**
     * @return void
     */
    public function testExportExceptionToArrayWithArrayableException(): void
    {
        $exception = new ApiException('T01', 'Teste', 404);

        $response = $this->handler->exportExceptionToArray($exception);

        $this->assertIsArray($response);
    }

    /**
     * @return void
     */
    public function testExportExceptionToArrayWithDefaultException(): void
    {
        $exception = new Exception('Teste', 100);

        $response = $this->handler->exportExceptionToArray($exception);

        $this->assertIsArray($response);
        $this->assertEquals(
            '{"exception-class":"Exception","message":"Teste","file":"\/var\/www\/logcomex-php-utils\/tests\/Handlers\/ExceptionHandlerUnitTest.php","line":56,"code":100}',
            json_encode($response)
        );
    }

    /**
     * @return void
     */
    public function testRenderUnknowException(): void
    {
        $exception = new Exception();
        $request = new Request();

        $response = $this->handler->render($request, $exception);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals('{"message":"Tivemos um erro inesperado.","error":"Internal server error","request":[]}', $response->getContent());
        $this->assertEquals(500, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testRenderAuthenticationException(): void
    {
        $exception = new AuthenticationException();
        $request = new Request();

        $response = $this->handler->render($request, $exception);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals('{"message":"N\u00e3o autenticado","error":"Unauthorized"}', $response->getContent());
        $this->assertEquals(401, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testRenderQueryException(): void
    {
        $exception = new QueryException('', [], new Exception());
        $request = new Request();

        $response = $this->handler->render($request, $exception);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals('{"message":"Tivemos um problema com nosso banco de dados","error":"Bad Gateway"}', $response->getContent());
        $this->assertEquals(502, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testRenderNotFoundHttpException(): void
    {
        $exception = new NotFoundHttpException();
        $request = new Request();

        $response = $this->handler->render($request, $exception);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals('{"message":"A p\u00e1gina solicitada n\u00e3o p\u00f4de ser encontrada","error":"Page not found"}', $response->getContent());
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testRenderValidationException(): void
    {
        try {
            Validator::make(['wrongKey' => 'test'], ['test' => 'required'])->validate();
        } catch (Exception $exception) {
            $request = new Request();
            $response = $this->handler->render($request, $exception);

            $this->assertInstanceOf(JsonResponse::class, $response);
            $this->assertEquals('{"message":"common.errors.data_invalid","error":{"test":["The test field is required."]}}', $response->getContent());
            $this->assertEquals(406, $response->getStatusCode());

            return;
        }

        $this->assertTrue(false, 'It was not possible to test this Exception');
    }

    /**
     * @return void
     */
    public function testRenderMethodNotAllowedHttpException(): void
    {
        $exception = new MethodNotAllowedHttpException([]);
        $request = new Request();

        $response = $this->handler->render($request, $exception);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals('{"message":"O m\u00e9todo para essa requisi\u00e7\u00e3o est\u00e1 incorreto.","error":"Method Not Allowed"}', $response->getContent());
        $this->assertEquals(405, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testRenderApiException(): void
    {
        $exception = new ApiException('', '');
        $request = new Request();

        $response = $this->handler->render($request, $exception);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals('{"message":""}', $response->getContent());
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testRenderBadImplementationException(): void
    {
        $exception = new BadImplementationException('Error', Response::HTTP_INTERNAL_SERVER_ERROR);
        $request = new Request();

        $response = $this->handler->render($request, $exception);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals('{"message":"Tivemos um erro na aplica\u00e7\u00e3o!"}', $response->getContent());
        $this->assertEquals(500, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testReport(): void
    {
        $exception = new ApiException('', '');

        $response = $this->handler->report($exception);

        $this->assertNull($response);
    }
}
