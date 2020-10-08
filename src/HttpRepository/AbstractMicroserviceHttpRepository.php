<?php

namespace Mtarld\ApiPlatformMsBundle\HttpRepository;

use Mtarld\ApiPlatformMsBundle\Collection\Collection;
use Mtarld\ApiPlatformMsBundle\Dto\ApiResourceDtoInterface;
use Mtarld\ApiPlatformMsBundle\HttpClient\GenericHttpClient;
use Mtarld\ApiPlatformMsBundle\Microservice\Microservice;
use Mtarld\ApiPlatformMsBundle\Microservice\MicroservicePool;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class AbstractMicroserviceHttpRepository
{
    protected $serializer;
    protected $httpClient;

    private $microservices;

    public function __construct(
        GenericHttpClient $httpClient,
        SerializerInterface $serializer,
        MicroservicePool $microservices
    ) {
        $this->httpClient = $httpClient;
        $this->serializer = $serializer;
        $this->microservices = $microservices;
    }

    abstract protected function getMicroserviceName(): string;

    abstract protected function getResourceEndpoint(): string;

    abstract protected function getResourceDto(): string;

    /**
     * @psalm-param array<mixed> $additionalQueryParams
     *
     * @throws HttpExceptionInterface
     */
    public function findOneByIri(string $iri, array $additionalQueryParams = []): ?ApiResourceDtoInterface
    {
        try {
            if (!empty($additionalQueryParams)) {
                $iri .= '?'.http_build_query($additionalQueryParams);
            }

            /** @var ApiResourceDtoInterface|null $resource */
            $resource = $this->serializer->deserialize(
                $this->request('GET', $iri)->getContent(),
                $this->getResourceDto(),
                $this->getMicroservice()->getFormat()
            );

            return $resource;
        } catch (ClientExceptionInterface $e) {
            if (404 === $e->getCode()) {
                return null;
            }

            throw $e;
        }
    }

    /**
     * @psalm-param scalar $value
     * @psalm-param array<mixed> $additionalQueryParams
     *
     * @throws ExceptionInterface
     */
    public function findOneBy(string $property, $value, array $additionalQueryParams = []): ?ApiResourceDtoInterface
    {
        $items = iterator_to_array($this->findBy($property, [$value], $additionalQueryParams));
        $item = reset($items);

        return false !== $item ? $item : null;
    }

    /**
     * @psalm-param array<scalar> $values
     * @psalm-param array<mixed> $additionalQueryParams
     * @psalm-return Collection<ApiResourceDtoInterface>
     *
     * @throws ExceptionInterface
     */
    public function findBy(string $property, array $values, array $additionalQueryParams = []): Collection
    {
        return $this->requestCollection([$property => $values] + $additionalQueryParams);
    }

    /**
     * @psalm-return Collection<ApiResourceDtoInterface>
     * @psalm-param array<mixed> $additionalQueryParams
     *
     * @throws ExceptionInterface
     */
    public function findAll(array $additionalQueryParams = []): Collection
    {
        return $this->requestCollection($additionalQueryParams);
    }

    /**
     * @psalm-param array<mixed> $queryParams
     * @psalm-return Collection<ApiResourceDtoInterface>
     *
     * @throws ExceptionInterface
     */
    protected function requestCollection(array $queryParams = []): Collection
    {
        $uri = $this->getResourceEndpoint();
        if (!empty($queryParams)) {
            $uri .= '?'.http_build_query($queryParams);
        }
        $response = $this->request('GET', $uri);

        /** @var Collection $collection */
        $collection = $this->serializer->deserialize(
            $response->getContent(),
            Collection::class.'<'.$this->getResourceDto().'>',
            $this->getMicroservice()->getFormat()
        );

        return $collection->withMicroservice($this->getMicroservice());
    }

    /**
     * @param mixed $body
     *
     * @throws ExceptionInterface
     */
    protected function request(string $method, string $uri, $body = null): ResponseInterface
    {
        return $this->httpClient->request($this->getMicroservice(), $method, $uri, $body);
    }

    private function getMicroservice(): Microservice
    {
        return $this->microservices->get($this->getMicroserviceName());
    }
}
