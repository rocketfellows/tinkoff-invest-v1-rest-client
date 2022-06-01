<?php

namespace rocketfellows\TinkoffInvestV1HttpClient;

use GuzzleHttp\Client as HttpClient;

class Client
{
    private $config;
    private $httpClient;

    public function __construct(ClientConfig $config, HttpClient $httpClient)
    {
        $this->config = $config;
        $this->httpClient = $httpClient;
    }
}
