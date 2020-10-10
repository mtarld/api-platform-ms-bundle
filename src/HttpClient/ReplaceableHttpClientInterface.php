<?php

namespace Mtarld\ApiPlatformMsBundle\HttpClient;

use Symfony\Contracts\HttpClient\HttpClientInterface;

interface ReplaceableHttpClientInterface
{
    public function setHttpClient(HttpClientInterface $httpClient): void;
}
