<?php

namespace Mtarld\ApiPlatformMsBundle\HttpClient;

use Mtarld\ApiPlatformMsBundle\Microservice\Microservice;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @final
 */
class GenericHttpClient
{
    private const MIME_TYPES = [
        'jsonld' => 'application/ld+json',
        'jsonapi' => 'application/vnd.api+json',
        'jsonhal' => 'application/hal+json',
    ];

    private $serializer;
    private $httpClient;

    /**
     * @var iterable<AuthenticationHeaderProviderInterface>
     */
    private $authenticationHeaderProviders;

    public function __construct(
        SerializerInterface $serializer,
        HttpClientInterface $httpClient,
        iterable $authenticationHeaderProviders = []
    ) {
        $this->serializer = $serializer;
        $this->httpClient = $httpClient;
        $this->authenticationHeaderProviders = $authenticationHeaderProviders;
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

        // Only the first authentication header provider that supports the context will add the authentication header.
        foreach ($this->authenticationHeaderProviders as $provider) {
            if ($provider->supports(['method' => $method, 'uri' => $uri, 'options' => $options, 'microservice' => $microservice])) {
                $options['headers'] += [$provider->getHeader() => $provider->getValue()];

                break;
            }
        }

        return $this->httpClient->request($method, $uri, $options);
    }

    public function getWrappedHttpClient(): HttpClientInterface
    {
        return $this->httpClient;
    }

    public function setWrappedHttpClient(HttpClientInterface $httpClient): void
    {
        $this->httpClient = $httpClient;
    }
}
