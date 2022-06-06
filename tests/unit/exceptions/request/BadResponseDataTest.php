<?php

namespace rocketfellows\TinkoffInvestV1RestClient\tests\unit\exceptions\request;

use PHPUnit\Framework\TestCase;
use rocketfellows\TinkoffInvestV1RestClient\exceptions\request\BadResponseData;

/**
 * @group exceptions
 */
class BadResponseDataTest extends TestCase
{
    /**
     * @dataProvider getBadResponseProvidedData
     */
    public function testSuccessInitialization(?int $code, ?string $message, ?string $description): void
    {
        $data = new BadResponseData($code, $message, $description);

        $this->assertEquals($code, $data->getCode());
        $this->assertEquals($message, $data->getMessage());
        $this->assertEquals($description, $data->getDescription());
    }

    public function getBadResponseProvidedData(): array
    {
        return [
            [
                'code' => 1,
                'message' => 'message',
                'description' => 'description',
            ],
            [
                'code' => null,
                'message' => 'message',
                'description' => 'description',
            ],
            [
                'code' => 1,
                'message' => null,
                'description' => 'description',
            ],
            [
                'code' => 1,
                'message' => 'message',
                'description' => null,
            ],
            [
                'code' => null,
                'message' => null,
                'description' => null,
            ],
        ];
    }
}
