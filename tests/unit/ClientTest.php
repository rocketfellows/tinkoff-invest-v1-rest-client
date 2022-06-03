<?php

namespace rocketfellows\TinkoffInvestV1RestClient\tests\unit;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException as GuzzleClientException;
use GuzzleHttp\Exception\ServerException as GuzzleServerException;
use PHPUnit\Framework\MockObject\Builder\InvocationMocker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use rocketfellows\TinkoffInvestV1RestClient\Client;
use rocketfellows\TinkoffInvestV1RestClient\ClientConfig;
use Throwable;

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

    /**
     * @dataProvider getSuccessRequestProvidedData
     */
    public function testSuccessRequest(
        string $serviceName,
        string $serviceMethod,
        array $requestData,
        string $httpClientResponse,
        string $expectedRequestUri,
        array $expectedRequestOptions,
        array $expectedResponse
    ): void {
        $this->assertHttpClientSendRequest($expectedRequestUri, $expectedRequestOptions, $this->getResponseMock($httpClientResponse));

        $this->assertEquals($expectedResponse, $this->client->request($serviceName, $serviceMethod, $requestData));
    }

    public function getSuccessRequestProvidedData(): array
    {
        return [
            'httpClientReturnsEmptyResponse' => [
                'serviceName' => 'ServiceName',
                'serviceMethod' => 'ServiceMethod',
                'requestData' => [],
                'httpClientResponse' => '',
                'expectedRequestUri' => self::SERVER_URL_TEST_VALUE . '/tinkoff.public.invest.api.contract.v1.ServiceName/ServiceMethod',
                'expectedRequestOptions' => [
                    'headers' => [
                        'Authorization' => 'Bearer ' . self::ACCESS_TOKEN_TEST_VALUE,
                        'Accept' => 'application/json',
                    ],
                    'json' => [],
                ],
                'expectedResponse' => [],
            ],
            'httpClientReturnsComplexJsonResponse' => [
                'serviceName' => 'fooName',
                'serviceMethod' => 'barName',
                'requestData' => ['foo' => 'bar', 'fooBar' => ['bar' => 'foo', 'foo' => true,],],
                'httpClientResponse' => '{"foo":"bar","bar":1,"fooBar":[1,3,2],"barFoo":{"foo":true,"bar":10.4}}',
                'expectedRequestUri' => self::SERVER_URL_TEST_VALUE . '/tinkoff.public.invest.api.contract.v1.fooName/barName',
                'expectedRequestOptions' => [
                    'headers' => [
                        'Authorization' => 'Bearer ' . self::ACCESS_TOKEN_TEST_VALUE,
                        'Accept' => 'application/json',
                    ],
                    'json' => ['foo' => 'bar', 'fooBar' => ['bar' => 'foo', 'foo' => true,],],
                ],
                'expectedResponse' => ['foo' => 'bar', 'bar' => 1, 'fooBar' => [1, 3, 2,], 'barFoo' => ['foo' => true, 'bar' => 10.4],],
            ],
            'httpClientReturnsBrokenJsonResponse' => [
                'serviceName' => 'fooName',
                'serviceMethod' => 'barName',
                'requestData' => ['foo' => 'bar', 'fooBar' => ['bar' => 'foo', 'foo' => true,],],
                'httpClientResponse' => '{:"bar","bar":1,"fooBar":[1,3,2],"barFoo":{"foo":true,"bar":10.4}}',
                'expectedRequestUri' => self::SERVER_URL_TEST_VALUE . '/tinkoff.public.invest.api.contract.v1.fooName/barName',
                'expectedRequestOptions' => [
                    'headers' => [
                        'Authorization' => 'Bearer ' . self::ACCESS_TOKEN_TEST_VALUE,
                        'Accept' => 'application/json',
                    ],
                    'json' => ['foo' => 'bar', 'fooBar' => ['bar' => 'foo', 'foo' => true,],],
                ],
                'expectedResponse' => [],
            ],
        ];
    }

    private function assertHttpClientSendRequest(string $uri, array $options, MockObject $response): void
    {
        $this->assertHttpClientCallRequest($uri, $options)->willReturn($response);
    }

    private function assertHttpClientRequestThrowsClientException(string $uri, array $options, string $responseBody): void
    {
        $exception = $this->createMock(GuzzleClientException::class);
        $exception->method('getResponse')->willReturn($this->getResponseMock($responseBody));

        $this->assertHttpClientRequestThrowsException($uri, $options, $exception);
    }

    private function assertHttpClientRequestThrowsServerException(string $uri, array $options, string $responseBody): void
    {
        $exception = $this->createMock(GuzzleServerException::class);
        $exception->method('getResponse')->willReturn($this->getResponseMock($responseBody));

        $this->assertHttpClientRequestThrowsException($uri, $options, $exception);
    }

    private function assertHttpClientRequestThrowsException(string $uri, array $options, Throwable $exception): InvocationMocker
    {
        return $this->assertHttpClientCallRequest($uri, $options)->willThrowException($exception);
    }

    private function assertHttpClientCallRequest(string $uri, array $options): InvocationMocker
    {
        return $this->httpClient->expects($this->once())->method('request')->with('POST', $uri, $options);
    }

    private function getResponseMock(string $responseBodyContent): MockObject
    {
        $responseBody = $this->createMock(StreamInterface::class);
        $responseBody->method('getContents')->willReturn($responseBodyContent);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn($responseBody);

        return $response;
    }
}
