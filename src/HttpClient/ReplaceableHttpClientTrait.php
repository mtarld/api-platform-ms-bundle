<?php

namespace Mtarld\ApiPlatformMsBundle\HttpClient;

use Symfony\Contracts\HttpClient\HttpClientInterface;

trait ReplaceableHttpClientTrait
{
    public function setHttpClient(HttpClientInterface $httpClient): void
    {
        $this->httpClient->setHttpClient($httpClient);
    }
}
