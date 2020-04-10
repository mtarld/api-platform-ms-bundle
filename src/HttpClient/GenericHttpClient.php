<?php

namespace Mtarld\ApiPlatformMsBundle\HttpClient;

use Mtarld\ApiPlatformMsBundle\Microservice\Microservice;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @final
 */
class GenericHttpClient
{
    private $container;
    private $serializer;
    private $httpClient;

    public function __construct(
        ContainerInterface $container,
        SerializerInterface $serializer,
        HttpClientInterface $httpClient
    ) {
        $this->container = $container;
        $this->serializer = $serializer;
        $this->httpClient = $httpClient;
    }

    /**
     * @param mixed $body
     *
     * @throws ExceptionInterface
     */
    public function request(Microservice $microservice, string $method, string $uri, $body = null, ?string $mimeType = null, ?string $bodyFormat = null): ResponseInterface
    {
        $mimeType = $mimeType ?? $this->container->getParameter('api_platform.formats')[$microservice->getFormat()][0];
        $bodyFormat = $bodyFormat ?? $microservice->getFormat();
        $options = [
            'base_uri' => $microservice->getBaseUri(),
            'headers' => [
                'Content-Type' => $mimeType,
                'Accept' => $mimeType,
            ],
        ];

        if (null !== $body) {
            $options['body'] = $this->serializer->serialize($body, $bodyFormat);
        }

        return $this->httpClient->request($method, $uri, $options);
    }
}
