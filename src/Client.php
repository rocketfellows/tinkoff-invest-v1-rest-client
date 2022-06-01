<?php

namespace rocketfellows\TinkoffInvestV1HttpClient;

use GuzzleHttp\Client as HttpClient;

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
     * TODO: handle exceptions
     * @param string $serviceUrl
     * @param string $serviceMethod
     * @param array $data
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request(string $serviceName, string $serviceMethod, array $data): array
    {
        $response = $this->httpClient->request(
            self::HTTP_REQUEST_METHOD,
            $this->getFullServicePath($this->config->getServerUrl(), $serviceName, $serviceMethod),
            $this->getRequestOptions($this->config->getAccessToken(), $data)
        );

        return json_decode($response->getBody()->getContents(), true);
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
}
