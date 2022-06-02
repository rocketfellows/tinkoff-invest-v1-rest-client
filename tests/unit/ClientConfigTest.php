<?php

namespace rocketfellows\TinkoffInvestV1RestClient\tests\unit;

use PHPUnit\Framework\TestCase;
use rocketfellows\TinkoffInvestV1RestClient\ClientConfig;
use rocketfellows\TinkoffInvestV1RestClient\exceptions\config\AccessTokenEmptyException;
use rocketfellows\TinkoffInvestV1RestClient\exceptions\config\ServerUrlEmptyException;

class ClientConfigTest extends TestCase
{
    private const SERVER_URL_TEST_VALUE = 'server_url_test_value';
    private const ACCESS_TOKEN_TEST_VALUE = 'access_token_test_value';

    public function testSuccessInitConfig(): void
    {
        $config = new ClientConfig(self::SERVER_URL_TEST_VALUE, self::ACCESS_TOKEN_TEST_VALUE);

        $this->assertEquals(self::SERVER_URL_TEST_VALUE, $config->getServerUrl());
        $this->assertEquals(self::ACCESS_TOKEN_TEST_VALUE, $config->getAccessToken());
    }

    public function testInitConfigWithEmptyServerUrlThrowsException(): void
    {
        $this->expectException(ServerUrlEmptyException::class);

        (new ClientConfig('', self::ACCESS_TOKEN_TEST_VALUE));
    }

    public function testInitConfigWithEmptyAccessTokenThrowsException(): void
    {
        $this->expectException(AccessTokenEmptyException::class);

        (new ClientConfig(self::SERVER_URL_TEST_VALUE, ''));
    }
}
