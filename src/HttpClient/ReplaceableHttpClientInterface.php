<?php

namespace Mtarld\ApiPlatformMsBundle\HttpClient;

use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
interface ReplaceableHttpClientInterface
{
    public function setWrappedHttpClient(HttpClientInterface $httpClient): void;
}
