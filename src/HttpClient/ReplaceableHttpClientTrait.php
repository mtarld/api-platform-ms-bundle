<?php

namespace Mtarld\ApiPlatformMsBundle\HttpClient;

use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @internal
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
trait ReplaceableHttpClientTrait
{
    public function setWrappedHttpClient(HttpClientInterface $httpClient): void
    {
        $this->httpClient->setWrappedHttpClient($httpClient);
    }
}
