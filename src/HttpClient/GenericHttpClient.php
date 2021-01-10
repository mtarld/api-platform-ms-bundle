<?php

namespace Mtarld\ApiPlatformMsBundle\HttpClient;

use Mtarld\ApiPlatformMsBundle\Microservice\Microservice;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @final
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class GenericHttpClient implements ReplaceableHttpClientInterface
{
    private const MIME_TYPES = [
        'jsonld' => 'application/ld+json',
        'jsonapi' => 'application/vnd.api+json',
        'jsonhal' => 'application/hal+json',
    ];

    private $serializer;
    private $httpClient;

    public function __construct(
        SerializerInterface $serializer,
        HttpClientInterface $httpClient
    ) {
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
        $mimeType = $mimeType ?? self::MIME_TYPES[$microservice->getFormat()];
        $bodyFormat = $bodyFormat ?? $microservice->getFormat();

        $options = [
            'base_uri' => $microservice->getBaseUri(),
            'headers' => [
                'Content-Type' => $mimeType,
                'Accept' => $mimeType,
            ],
        ];

        $uri = preg_replace('/^\/?'.preg_quote($microservice->getApiPath(), '/').'/', '', $uri);
        $uri = rtrim($microservice->getApiPath(), '/').'/'.ltrim($uri, '/');

        if (null !== $body) {
            $options['body'] = $this->serializer->serialize($body, $bodyFormat);
        }

        return $this->httpClient->request($method, $uri, $options);
    }

    public function setWrappedHttpClient(HttpClientInterface $httpClient): void
    {
        $this->httpClient = $httpClient;
    }
}
