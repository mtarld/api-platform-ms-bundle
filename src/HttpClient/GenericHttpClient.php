<?php

namespace Mtarld\ApiPlatformMsBundle\HttpClient;

use Mtarld\ApiPlatformMsBundle\Event\RequestEvent;
use Mtarld\ApiPlatformMsBundle\Microservice\Microservice;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

// Help opcache.preload discover always-needed symbols
class_exists(RequestEvent::class);

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

    private const PATCH_MIME_TYPES = [
        'jsonld' => 'application/merge-patch+json',
        'jsonapi' => 'application/vnd.api+json',
        'jsonhal' => 'application/merge-patch+json',
    ];

    /**
     * @var iterable<AuthenticationHeaderProviderInterface>
     */
    private iterable $authenticationHeaderProviders;

    public function __construct(
        private SerializerInterface $serializer,
        private HttpClientInterface $httpClient,
        iterable                    $authenticationHeaderProviders = [],
        private ?EventDispatcherInterface $dispatcher = null
    ) {
        $this->authenticationHeaderProviders = $authenticationHeaderProviders;
    }

    /**
     * @throws ExceptionInterface
     */
    public function request(Microservice $microservice, string $method, string $uri, mixed $body = null, ?string $mimeType = null, ?string $bodyFormat = null): ResponseInterface
    {
        $defaultMimeType = 'PATCH' === $method ? self::PATCH_MIME_TYPES[$microservice->getFormat()] : self::MIME_TYPES[$microservice->getFormat()];
        $mimeType = $mimeType ?? $defaultMimeType;
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

        $response = $this->httpClient->request($method, $uri, $options);

        if (null !== $this->dispatcher) {
            $this->dispatcher->dispatch(new RequestEvent($microservice, $method, $uri, $options));
        }

        return $response;
    }

    public function setWrappedHttpClient(HttpClientInterface $httpClient): void
    {
        $this->httpClient = $httpClient;
    }
}
