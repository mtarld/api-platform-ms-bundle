<?php

namespace Mtarld\ApiPlatformMsBundle\Resource;

use Mtarld\ApiPlatformMsBundle\Microservice\MicroservicePool;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ExistenceChecker
{
    private $httpClient;
    private $serializer;
    private $microservices;

    public function __construct(
        HttpClientInterface $httpClient,
        SerializerInterface $serializer,
        MicroservicePool $microservices
    ) {
        $this->httpClient = $httpClient;
        $this->serializer = $serializer;
        $this->microservices = $microservices;
    }

    /**
     * @param array<string> $iris
     *
     * @return array<array-key, bool>
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getExistenceStatuses(string $microserviceName, array $iris): array
    {
        $response = $this->httpClient->request('POST', sprintf('/%s_check_resource', $microserviceName), [
            'base_uri' => $this->microservices->get($microserviceName)->getBaseUri(),
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept: application/json',
            ],
            'body' => $this->serializer->serialize(new ExistenceCheckerPayload($iris), 'json'),
        ]);

        /** @var ExistenceCheckerView $checkedIris */
        $checkedIris = $this->serializer->deserialize($response->getContent(), ExistenceCheckerView::class, 'json');

        return $checkedIris->existences;
    }
}
