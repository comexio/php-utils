<?php

namespace Logcomex\PhpUtils\Exceptions;

use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Http\Response;

/**
 * Class UnavailableServiceException
 * @package Logcomex\PhpUtils\Exceptions
 */
class UnavailableServiceException extends Exception implements Arrayable, Jsonable
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
     * @var string
     */
    private $service;

    /**
     * ApiException constructor.
     * @param string $token
     * @param string $reason
     * @param string $service
     * @param int $httpCode
     * @param Exception|null $previous
     */
    public function __construct(
        string $token,
        string $reason,
        string $service,
        int $httpCode = Response::HTTP_SERVICE_UNAVAILABLE,
        Exception $previous = null
    )
    {
        $this->token = $token;
        $this->httpCode = $httpCode;
        $this->service = $service;
        parent::__construct($reason, $httpCode, $previous);
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
    public function getService()
    {
        return $this->service;
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
            'reason' => $this->getMessage(),
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
