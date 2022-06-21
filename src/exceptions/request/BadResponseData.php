<?php

namespace rocketfellows\TinkoffInvestV1RestClient\exceptions\request;

class BadResponseData
{
    private $code;
    private $message;
    private $description;

    public function __construct(
        ?int $code = null,
        ?string $message = null,
        ?string $description = null
    ) {
        $this->code = $code;
        $this->message = $message;
        $this->description = $description;
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
