<?php

namespace rocketfellows\TinkoffInvestV1HttpClient;

use rocketfellows\TinkoffInvestV1HttpClient\exceptions\config\AccessTokenEmptyException;
use rocketfellows\TinkoffInvestV1HttpClient\exceptions\config\ServerUrlEmptyException;

class ClientConfig
{
    private $serverUrl;
    private $accessToken;

    /**
     * @throws AccessTokenEmptyException
     * @throws ServerUrlEmptyException
     */
    public function __construct(string $serverUrl, string $accessToken)
    {
        $this->serverUrl = $serverUrl;
        $this->accessToken = $accessToken;

        $this->validateConfig();
    }

    public function getServerUrl(): string
    {
        return $this->serverUrl;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @throws AccessTokenEmptyException
     * @throws ServerUrlEmptyException
     */
    private function validateConfig(): void
    {
        if (empty($this->getServerUrl())) {
            throw new ServerUrlEmptyException();
        }

        if (empty($this->getAccessToken())) {
            throw new AccessTokenEmptyException();
        }
    }
}
