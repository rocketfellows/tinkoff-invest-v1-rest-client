<?php

namespace rocketfellows\TinkoffInvestV1RestClient\exceptions\request;

use Throwable;

abstract class BadResponseException extends RequestException
{
    protected $data;

    public function __construct(
        BadResponseData $data,
        string $message = "",
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->data = $data;
    }

    public function getErrorCode(): ?int
    {
        return $this->data->getCode();
    }

    public function getErrorMessage(): ?string
    {
        return $this->data->getMessage();
    }

    public function getErrorDescription(): ?string
    {
        return $this->data->getDescription();
    }
}
