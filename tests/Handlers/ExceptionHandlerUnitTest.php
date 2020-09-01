<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Logcomex\PhpUtils\Exceptions\BadImplementationException;
use Logcomex\PhpUtils\Exceptions\SecurityException;
use Logcomex\PhpUtils\Exceptions\UnavailableServiceException;
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

        $response = $this->handler::exportExceptionToArray($exception);

        $this->assertIsArray($response);
    }

    /**
     * @return void
     */
    public function testExportExceptionToArrayWithDefaultException(): void
    {
        $exception = new Exception('Teste', 100);

        $exceptionArray = $this->handler::exportExceptionToArray($exception);
        $this->assertIsArray($exceptionArray);
        $this->assertEquals('Exception', $exceptionArray['exception-class']);
        $this->assertEquals('Teste', $exceptionArray['message']);
        $this->assertNotNull($exceptionArray['file']);
        $this->assertNotNull($exceptionArray['line']);
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
        $this->assertEquals('{"message":"","code":""}', $response->getContent());
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testRenderBadImplementationException(): void
    {
        $exception = new BadImplementationException('B1-004', 'Error');
        $request = new Request();

        $response = $this->handler->render($request, $exception);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(
            '{"code":"B1-004","message":"Tivemos um erro na aplica\u00e7\u00e3o!"}',
            $response->getContent()
        );
        $this->assertEquals(500, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testRenderSecurityException(): void
    {
        $exception = new SecurityException('SEC01', "It's not safe");
        $request = new Request();

        $response = $this->handler->render($request, $exception);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals('{"code":"SEC01","message":"Tivemos um erro de seguran\u00e7a na aplica\u00e7\u00e3o!","reason":"It\'s not safe"}', $response->getContent());
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testRenderUnavailableServiceException(): void
    {
        $exception = new UnavailableServiceException(
            'XXX001',
            "Não foi possível fazer requisição",
            'test-1'
        );
        $request = new Request();

        $response = $this->handler->render($request, $exception);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(
            '{"code":"XXX001","message":"Service test-1 apresenta problemas!","service":"test-1","reason":"N\u00e3o foi poss\u00edvel fazer requisi\u00e7\u00e3o"}',
            $response->getContent()
        );
        $this->assertEquals(503, $response->getStatusCode());
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
