<?php

namespace Mtarld\ApiPlatformMsBundle\HttpClient;

use Mtarld\ApiPlatformMsBundle\Microservice\MicroservicePool;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @final
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class MicroserviceHttpClient implements MicroserviceHttpClientInterface
{
    use ReplaceableHttpClientTrait;

    private $httpClient;

    public function __construct(
        GenericHttpClient $httpClient,
        private readonly MicroservicePool $microservices,
        private readonly string $microserviceName
    ) {
        $this->httpClient = $httpClient;
    }

    public function request(string $method, string $uri, $body = null, ?string $mimeType = null, ?string $bodyFormat = null): ResponseInterface
    {
        return $this->httpClient->request($this->microservices->get($this->microserviceName), $method, $uri, $body, $mimeType, $bodyFormat);
    }
}
