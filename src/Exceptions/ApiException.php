<?php

namespace Logcomex\PhpUtils\Exceptions;

use Exception;
use Illuminate\Http\Response;

/**
 * Class ApiException
 * @package Logcomex\PhpUtils\Exceptions
 */
class ApiException extends BaseException
{
    /**
     * @var int
     */
    private $httpCode;
    /**
     * @var string
     */
    private $token;

    /**
     * ApiException constructor.
     * @param string $token
     * @param string $message
     * @param int $httpCode
     * @param Exception|null $previous
     */
    public function __construct(
        string $token,
        string $message,
        int $httpCode = Response::HTTP_BAD_REQUEST,
        Exception $previous = null
    )
    {
        $this->token = $token;
        $this->httpCode = $httpCode;
        parent::__construct($message, $httpCode, $previous);
    }

    /**
     * @return int
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return __CLASS__ . ": [{$this->token}]: {$this->message}";
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'exception-class' => class_basename($this),
            'message' => $this->getMessage(),
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'http-code' => $this->getHttpCode(),
            'token' => $this->getToken()
        ];
    }

    /**
     * @param int $options
     * @return string
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
