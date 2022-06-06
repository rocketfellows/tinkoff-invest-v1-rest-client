<?php

namespace rocketfellows\TinkoffInvestV1RestClient\tests\unit\exceptions\request;

use rocketfellows\TinkoffInvestV1RestClient\exceptions\request\BadResponseData;
use rocketfellows\TinkoffInvestV1RestClient\exceptions\request\BadResponseException;
use rocketfellows\TinkoffInvestV1RestClient\exceptions\request\ServerException;
use Throwable;

/**
 * @group exceptions
 */
class ServerExceptionTest extends BadResponseExceptionTest
{
    public function instantiateException(
        ?int $badResponseCode,
        ?string $badResponseMessage,
        ?string $badResponseDescription,
        ?string $message,
        ?int $code,
        ?Throwable $previousException
    ): BadResponseException {
        return new ServerException(
            (new BadResponseData($badResponseCode, $badResponseMessage, $badResponseDescription)),
            $message,
            $code,
            $previousException
        );
    }
}
