<?php

namespace Mtarld\ApiPlatformMsBundle\HttpClient;

use Mtarld\ApiPlatformMsBundle\Microservice\MicroservicePool;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @final
 */
class MicroserviceHttpClient implements MicroserviceHttpClientInterface
{
    use ReplaceableHttpClientTrait;

    private $httpClient;
    private $microservices;
    private $microserviceName;

    public function __construct(
        GenericHttpClient $httpClient,
        MicroservicePool $microservices,
        string $microserviceName
    ) {
        $this->httpClient = $httpClient;
        $this->microservices = $microservices;
        $this->microserviceName = $microserviceName;
    }

    public function request(string $method, string $uri, $body = null, ?string $mimeType = null, ?string $bodyFormat = null): ResponseInterface
    {
        return $this->httpClient->request($this->microservices->get($this->microserviceName), $method, $uri, $body, $mimeType, $bodyFormat);
    }
}
