<?php

namespace rocketfellows\TinkoffInvestV1RestClient\tests\unit;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException as GuzzleClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException as GuzzleServerException;
use PHPUnit\Framework\MockObject\Builder\InvocationMocker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use rocketfellows\TinkoffInvestV1RestClient\Client;
use rocketfellows\TinkoffInvestV1RestClient\ClientConfig;
use rocketfellows\TinkoffInvestV1RestClient\exceptions\request\BadResponseException;
use rocketfellows\TinkoffInvestV1RestClient\exceptions\request\ClientException;
use rocketfellows\TinkoffInvestV1RestClient\exceptions\request\HttpClientException;
use rocketfellows\TinkoffInvestV1RestClient\exceptions\request\ServerException;
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
     * @dataProvider getRequestProvidedData
     */
    public function testHttpClientThrowsUnknownGuzzleBadResponseException(
        string $serviceName,
        string $serviceMethod,
        string $expectedRequestUri,
        array $expectedRequestOptions
    ): void {
        $this->expectException(HttpClientException::class);
        $this->assertHttpClientThrowsUnknownGuzzleBadResponseException($expectedRequestUri, $expectedRequestOptions);

        $this->client->request($serviceName, $serviceMethod, []);
    }

    /**
     * @dataProvider getRequestProvidedData
     */
    public function testHttpClientThrowsGuzzleException(
        string $serviceName,
        string $serviceMethod,
        string $expectedRequestUri,
        array $expectedRequestOptions
    ): void {
        $this->expectException(HttpClientException::class);
        $this->assertHttpClientThrowsGuzzleException($expectedRequestUri, $expectedRequestOptions);

        $this->client->request($serviceName, $serviceMethod, []);
    }

    public function getRequestProvidedData(): array
    {
        return [
            [
                'serviceName' => 'ServiceName',
                'serviceMethod' => 'ServiceMethod',
                'expectedRequestUri' => self::SERVER_URL_TEST_VALUE . '/tinkoff.public.invest.api.contract.v1.ServiceName/ServiceMethod',
                'expectedRequestOptions' => [
                    'headers' => [
                        'Authorization' => 'Bearer ' . self::ACCESS_TOKEN_TEST_VALUE,
                        'Accept' => 'application/json',
                    ],
                    'json' => [],
                ],
            ],
        ];
    }

    /**
     * @dataProvider getHttpBadResponseThrowableProvidedData
     */
    public function testHttpClientThrowsServerException(
        string $serviceName,
        string $serviceMethod,
        string $clientExceptionResponseBody,
        string $expectedRequestUri,
        array $expectedRequestOptions,
        array $expectedExceptionData
    ): void {
        $exceptionThrown = false;

        try {
            $this->assertHttpClientRequestThrowsServerException(
                $expectedRequestUri,
                $expectedRequestOptions,
                $clientExceptionResponseBody
            );

            $this->client->request($serviceName, $serviceMethod, []);
        } catch (ServerException $exception) {
            $this->assertBadResponseExceptionData($exception, $expectedExceptionData);
            $exceptionThrown = true;
        }

        $this->assertTrue($exceptionThrown);
    }

    /**
     * @dataProvider getHttpBadResponseThrowableProvidedData
     */
    public function testHttpClientThrowsClientException(
        string $serviceName,
        string $serviceMethod,
        string $clientExceptionResponseBody,
        string $expectedRequestUri,
        array $expectedRequestOptions,
        array $expectedExceptionData
    ): void {
        $exceptionThrown = false;

        try {
            $this->assertHttpClientRequestThrowsClientException(
                $expectedRequestUri,
                $expectedRequestOptions,
                $clientExceptionResponseBody
            );

            $this->client->request($serviceName, $serviceMethod, []);
        } catch (ClientException $exception) {
            $this->assertBadResponseExceptionData($exception, $expectedExceptionData);
            $exceptionThrown = true;
        }

        $this->assertTrue($exceptionThrown);
    }

    public function getHttpBadResponseThrowableProvidedData(): array
    {
        return [
            'exceptionResponseAllParamsReturns' => [
                'serviceName' => 'ServiceName',
                'serviceMethod' => 'ServiceMethod',
                'clientExceptionResponseBody' => '{"code":1,"message":"foo","description":"bar"}',
                'expectedRequestUri' => self::SERVER_URL_TEST_VALUE . '/tinkoff.public.invest.api.contract.v1.ServiceName/ServiceMethod',
                'expectedRequestOptions' => [
                    'headers' => [
                        'Authorization' => 'Bearer ' . self::ACCESS_TOKEN_TEST_VALUE,
                        'Accept' => 'application/json',
                    ],
                    'json' => [],
                ],
                'expectedExceptionData' => ['code' => 1, 'message' => 'foo', 'description' => 'bar'],
            ],
            'exceptionResponseMoreThenExpectedParamsReturns' => [
                'serviceName' => 'ServiceName',
                'serviceMethod' => 'ServiceMethod',
                'clientExceptionResponseBody' => '{"code":1,"message":"foo","description":"bar", "foo":"foo"}',
                'expectedRequestUri' => self::SERVER_URL_TEST_VALUE . '/tinkoff.public.invest.api.contract.v1.ServiceName/ServiceMethod',
                'expectedRequestOptions' => [
                    'headers' => [
                        'Authorization' => 'Bearer ' . self::ACCESS_TOKEN_TEST_VALUE,
                        'Accept' => 'application/json',
                    ],
                    'json' => [],
                ],
                'expectedExceptionData' => ['code' => 1, 'message' => 'foo', 'description' => 'bar'],
            ],
            'exceptionParamCodeNotSetReturns' => [
                'serviceName' => 'ServiceName',
                'serviceMethod' => 'ServiceMethod',
                'clientExceptionResponseBody' => '{"message":"foo","description":"bar"}',
                'expectedRequestUri' => self::SERVER_URL_TEST_VALUE . '/tinkoff.public.invest.api.contract.v1.ServiceName/ServiceMethod',
                'expectedRequestOptions' => [
                    'headers' => [
                        'Authorization' => 'Bearer ' . self::ACCESS_TOKEN_TEST_VALUE,
                        'Accept' => 'application/json',
                    ],
                    'json' => [],
                ],
                'expectedExceptionData' => ['code' => null, 'message' => 'foo', 'description' => 'bar'],
            ],
            'exceptionParamMessageNotSetReturns' => [
                'serviceName' => 'ServiceName',
                'serviceMethod' => 'ServiceMethod',
                'clientExceptionResponseBody' => '{"description":"bar"}',
                'expectedRequestUri' => self::SERVER_URL_TEST_VALUE . '/tinkoff.public.invest.api.contract.v1.ServiceName/ServiceMethod',
                'expectedRequestOptions' => [
                    'headers' => [
                        'Authorization' => 'Bearer ' . self::ACCESS_TOKEN_TEST_VALUE,
                        'Accept' => 'application/json',
                    ],
                    'json' => [],
                ],
                'expectedExceptionData' => ['code' => null, 'message' => null, 'description' => 'bar'],
            ],
            'exceptionParamsInvalidJsonString' => [
                'serviceName' => 'ServiceName',
                'serviceMethod' => 'ServiceMethod',
                'clientExceptionResponseBody' => '{:}',
                'expectedRequestUri' => self::SERVER_URL_TEST_VALUE . '/tinkoff.public.invest.api.contract.v1.ServiceName/ServiceMethod',
                'expectedRequestOptions' => [
                    'headers' => [
                        'Authorization' => 'Bearer ' . self::ACCESS_TOKEN_TEST_VALUE,
                        'Accept' => 'application/json',
                    ],
                    'json' => [],
                ],
                'expectedExceptionData' => ['code' => null, 'message' => null, 'description' => null],
            ],
        ];
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

    private function assertHttpClientThrowsUnknownGuzzleBadResponseException(string $uri, array $options): void
    {
        $this->assertHttpClientRequestThrowsException(
            $uri,
            $options,
            $this->createMock(UnknownGuzzleBadResponseStubException::class)
        );
    }

    private function assertHttpClientThrowsGuzzleException(string $uri, array $options): void
    {
        $this->assertHttpClientRequestThrowsException($uri, $options, $this->createMock(GuzzleException::class));
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

    private function assertBadResponseExceptionData(BadResponseException $actualException, array $expectedData): void
    {
        $this->assertEquals($expectedData['code'], $actualException->getErrorCode());
        $this->assertEquals($expectedData['message'], $actualException->getErrorMessage());
        $this->assertEquals($expectedData['description'], $actualException->getErrorDescription());
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
