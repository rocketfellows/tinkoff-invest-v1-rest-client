<?php

namespace rocketfellows\TinkoffInvestV1RestClient\tests\unit;

use GuzzleHttp\Client as HttpClient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use rocketfellows\TinkoffInvestV1RestClient\Client;
use rocketfellows\TinkoffInvestV1RestClient\ClientConfig;

class ClientTest extends TestCase
{
    private const SERVER_URL_TEST_VALUE = 'server_url_test_value';
    private const ACCESS_TOKEN_TEST_VALUE = 'access_token_test_value';

    /**
     * @var Client
     */
    private $client;

    /**
     * @var HttpClient|MockObject
     */
    private $httpClient;

    /**
     * @var ClientConfig|MockObject
     */
    private $clientConfig;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clientConfig = new ClientConfig(self::SERVER_URL_TEST_VALUE, self::ACCESS_TOKEN_TEST_VALUE);
        $this->httpClient = $this->createMock(HttpClient::class);

        $this->client = new Client($this->clientConfig, $this->httpClient);
    }
}
