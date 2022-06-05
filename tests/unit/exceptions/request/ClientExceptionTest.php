<?php

namespace rocketfellows\TinkoffInvestV1RestClient\tests\unit\exceptions\request;

use rocketfellows\TinkoffInvestV1RestClient\exceptions\request\BadResponseData;
use rocketfellows\TinkoffInvestV1RestClient\exceptions\request\BadResponseException;
use rocketfellows\TinkoffInvestV1RestClient\exceptions\request\ClientException;
use Throwable;

class ClientExceptionTest extends BadResponseExceptionTest
{
    public function instantiateException(
        ?int $badResponseCode,
        ?string $badResponseMessage,
        ?string $badResponseDescription,
        ?string $message,
        ?int $code,
        ?Throwable $previousException
    ): BadResponseException {
        return new ClientException(
            (new BadResponseData($badResponseCode, $badResponseMessage, $badResponseDescription)),
            $message,
            $code,
            $previousException
        );
    }
}
