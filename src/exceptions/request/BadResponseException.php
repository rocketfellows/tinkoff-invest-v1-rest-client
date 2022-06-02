<?php

namespace rocketfellows\TinkoffInvestV1RestClient\exceptions\request;

use Throwable;

abstract class BadResponseException extends RequestException
{
    private $errorCode;
    private $errorMessage;
    private $errorDescription;

    public function __construct(
        ?int $errorCode = null,
        ?string $errorMessage = null,
        ?string $errorDescription = null,
        string $message = "",
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->errorCode = $errorCode;
        $this->errorMessage = $errorMessage;
        $this->errorDescription = $errorDescription;
    }

    public function getErrorCode(): ?int
    {
        return $this->errorCode;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function getErrorDescription(): ?string
    {
        return $this->errorDescription;
    }
}
