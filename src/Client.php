<?php

namespace rocketfellows\TinkoffInvestV1RestClient;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\BadResponseException as GuzzleBadResponseException;
use GuzzleHttp\Exception\ClientException as GuzzleClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException as GuzzleServerException;
use rocketfellows\TinkoffInvestV1RestClient\exceptions\request\BadResponseData;
use rocketfellows\TinkoffInvestV1RestClient\exceptions\request\ClientException;
use rocketfellows\TinkoffInvestV1RestClient\exceptions\request\HttpClientException;
use rocketfellows\TinkoffInvestV1RestClient\exceptions\request\ServerException;

class Client
{
    private const HTTP_REQUEST_METHOD = 'POST';
    private const MASK_FULL_SERVICE_PATH = '%s/%s/%s';
    private const MASK_SERVICE_PATH = 'tinkoff.public.invest.api.contract.v1.%s';
    private const MASK_AUTHORIZATION_HEADER = 'Bearer %s';

    private $config;
    private $httpClient;

    public function __construct(ClientConfig $config, HttpClient $httpClient)
    {
        $this->config = $config;
        $this->httpClient = $httpClient;
    }

    /**
     * @throws ServerException
     * @throws ClientException
     * @throws HttpClientException
     */
    public function request(string $serviceName, string $serviceMethod, array $data): array
    {
        try {
            $response = $this->httpClient->request(
                self::HTTP_REQUEST_METHOD,
                $this->getFullServicePath($this->config->getServerUrl(), $serviceName, $serviceMethod),
                $this->getRequestOptions($this->config->getAccessToken(), $data)
            );

            $responseData = json_decode($response->getBody()->getContents(), true);

            return ($responseData !== false && !is_null($responseData)) ? $responseData : [];
        } catch (GuzzleException $exception) {
            if (!$exception instanceof GuzzleBadResponseException) {
                throw new HttpClientException($exception->getMessage(), $exception->getCode(), $exception);
            }

            $badResponseData = $this->getBadResponseData($exception);

            if ($exception instanceof GuzzleClientException) {
                throw new ClientException($badResponseData, $exception->getMessage(), $exception->getCode(), $exception);
            }

            if ($exception instanceof GuzzleServerException) {
                throw new ServerException($badResponseData, $exception->getMessage(), $exception->getCode(), $exception);
            }

            throw new HttpClientException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    private function getFullServicePath(
        string $serverUrl,
        string $serviceName,
        string $serviceMethod
    ): string {
        return sprintf(
            self::MASK_FULL_SERVICE_PATH,
            $serverUrl,
            $this->getServicePath($serviceName),
            $serviceMethod
        );
    }

    private function getServicePath(string $serviceName): string
    {
        return sprintf(self::MASK_SERVICE_PATH, $serviceName);
    }

    private function getRequestOptions(string $accessToken, array $data): array
    {
        return [
            'headers' => $this->getRequestHeader($accessToken),
            'json' => $data,
        ];
    }

    private function getRequestHeader(string $accessToken): array
    {
        return [
            'Authorization' => $this->getAuthorizationHeader($accessToken),
            'Accept' => 'application/json',
        ];
    }

    private function getAuthorizationHeader(string $accessToken): string
    {
        return sprintf(self::MASK_AUTHORIZATION_HEADER, $accessToken);
    }

    private function getBadResponseData(GuzzleBadResponseException $exception): BadResponseData
    {
        $exceptionData = json_decode($exception->getResponse()->getBody()->getContents(), true);
        $exceptionData = ($exceptionData !== false && !is_null($exceptionData)) ? $exceptionData : [];
        return new BadResponseData(
            $exceptionData['code'] ?? null,
            $exceptionData['message'] ?? null,
            $exceptionData['description'] ?? null
        );
    }
}
