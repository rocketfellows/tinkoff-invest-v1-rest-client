<?php

namespace rocketfellows\TinkoffInvestV1RestClient\tests\unit\exceptions\request;

use Exception;
use PHPUnit\Framework\TestCase;
use rocketfellows\TinkoffInvestV1RestClient\exceptions\request\BadResponseException;
use Throwable;

/**
 * @group exceptions
 */
abstract class BadResponseExceptionTest extends TestCase
{
    abstract public function instantiateException(
        ?int $badResponseCode,
        ?string $badResponseMessage,
        ?string $badResponseDescription,
        ?string $message,
        ?int $code,
        ?Throwable $previousException
    ): BadResponseException;

    /**
     * @dataProvider getBadResponseProvidedData
     */
    public function testSuccessInitialization(
        ?int $badResponseCode,
        ?string $badResponseMessage,
        ?string $badResponseDescription,
        ?string $message,
        ?int $code,
        ?Throwable $previousException
    ): void {
        $exception = $this->instantiateException(
            $badResponseCode,
            $badResponseMessage,
            $badResponseDescription,
            $message,
            $code,
            $previousException
        );

        $this->assertEquals($badResponseCode, $exception->getErrorCode());
        $this->assertEquals($badResponseMessage, $exception->getErrorMessage());
        $this->assertEquals($badResponseDescription, $exception->getErrorDescription());
        $this->assertEquals($code, $exception->getCode());
        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals($previousException, $exception->getPrevious());
    }

    public function getBadResponseProvidedData(): array
    {
        return [
            [
                'badResponseCode' => 1,
                'badResponseMessage' => 'message',
                'badResponseDescription' => 'description',
                'message' => 'foo',
                'code' => 2,
                'previousException' => new Exception(),
            ],
            [
                'badResponseCode' => null,
                'badResponseMessage' => 'message',
                'badResponseDescription' => 'description',
                'message' => 'foo',
                'code' => 2,
                'previousException' => new Exception(),
            ],
            [
                'badResponseCode' => 1,
                'badResponseMessage' => null,
                'badResponseDescription' => 'description',
                'message' => 'foo',
                'code' => 2,
                'previousException' => new Exception(),
            ],
            [
                'badResponseCode' => 1,
                'badResponseMessage' => 'message',
                'badResponseDescription' => null,
                'message' => 'foo',
                'code' => 2,
                'previousException' => new Exception(),
            ],
            [
                'badResponseCode' => null,
                'badResponseMessage' => null,
                'badResponseDescription' => null,
                'message' => 'foo',
                'code' => 2,
                'previousException' => new Exception(),
            ],
        ];
    }
}
