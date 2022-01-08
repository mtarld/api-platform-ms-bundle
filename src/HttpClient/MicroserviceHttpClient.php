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

    public function __construct(
        private GenericHttpClient $httpClient,
        private MicroservicePool $microservices,
        private string $microserviceName
    ) {
    }

    public function request(string $method, string $uri, mixed $body = null, ?string $mimeType = null, ?string $bodyFormat = null): ResponseInterface
    {
        return $this->httpClient->request($this->microservices->get($this->microserviceName), $method, $uri, $body, $mimeType, $bodyFormat);
    }
}
