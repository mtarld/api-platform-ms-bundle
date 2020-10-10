<?php

namespace Mtarld\ApiPlatformMsBundle\HttpClient;

use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @internal
 */
interface ReplaceableHttpClientInterface
{
    public function setWrappedHttpClient(HttpClientInterface $httpClient): void;
}
