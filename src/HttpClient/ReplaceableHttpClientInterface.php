<?php

namespace Mtarld\ApiPlatformMsBundle\HttpClient;

use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @internal
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
interface ReplaceableHttpClientInterface
{
    public function setWrappedHttpClient(HttpClientInterface $httpClient): void;
}
