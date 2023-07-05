<?php

namespace Mtarld\ApiPlatformMsBundle\HttpClient;

use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
interface MicroserviceHttpClientInterface extends ReplaceableHttpClientInterface
{
    /**
     * @throws HttpExceptionInterface
     */
    public function request(string $method, string $uri, mixed $body = null, string $mimeType = null, string $bodyFormat = null): ResponseInterface;
}
