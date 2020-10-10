<?php

namespace Mtarld\ApiPlatformMsBundle\HttpClient;

use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @internal
 */
trait ReplaceableHttpClientTrait
{
    public function setWrappedHttpClient(HttpClientInterface $httpClient): void
    {
        $this->httpClient->setWrappedHttpClient($httpClient);
    }
}
