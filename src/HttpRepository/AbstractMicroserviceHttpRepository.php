<?php

namespace Mtarld\ApiPlatformMsBundle\HttpRepository;

use Mtarld\ApiPlatformMsBundle\Collection\Collection;
use Mtarld\ApiPlatformMsBundle\Dto\ApiResourceDtoInterface;
use Mtarld\ApiPlatformMsBundle\Exception\ResourceValidationException;
use Mtarld\ApiPlatformMsBundle\HttpClient\GenericHttpClient;
use Mtarld\ApiPlatformMsBundle\HttpClient\ReplaceableHttpClientInterface;
use Mtarld\ApiPlatformMsBundle\HttpClient\ReplaceableHttpClientTrait;
use Mtarld\ApiPlatformMsBundle\Microservice\Microservice;
use Mtarld\ApiPlatformMsBundle\Microservice\MicroservicePool;
use RuntimeException;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\Exception\RedirectionException;
use Symfony\Component\HttpClient\Exception\ServerException;
use Symfony\Component\Serializer\Exception\ExceptionInterface as SerializerExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

// Help opcache.preload discover always-needed symbols
class_exists(ClientException::class);
class_exists(ClientExceptionInterface::class);
class_exists(Collection::class);
class_exists(ConstraintViolationList::class);
class_exists(RedirectionException::class);
class_exists(ResourceValidationException::class);
class_exists(ServerException::class);
class_exists(SerializerExceptionInterface::class);

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
abstract class AbstractMicroserviceHttpRepository implements ReplaceableHttpClientInterface
{
    use ReplaceableHttpClientTrait;

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
     * @psalm-param array<array-key, mixed> $additionalQueryParams
     *
     * @throws ExceptionInterface
     */
    public function fetchOneByIri(string $iri, array $additionalQueryParams = []): ?ApiResourceDtoInterface
    {
        try {
            /** @var ApiResourceDtoInterface|null $resource */
            $resource = $this->serializer->deserialize(
                $this->request('GET', $this->buildUri($iri, $additionalQueryParams))->getContent(),
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
     * @deprecated since version 0.3.0, will be removed in 1.0. Use {@see \Mtarld\ApiPlatformMsBundle\HttpRepository\AbstractMicroserviceHttpRepository::fetchOneByIri()} instead.
     *
     * @psalm-param array<array-key, mixed> $additionalQueryParams
     *
     * @throws ExceptionInterface
     */
    public function findOneByIri(string $iri, array $additionalQueryParams = []): ?ApiResourceDtoInterface
    {
        return $this->fetchOneByIri($iri, $additionalQueryParams);
    }

    /**
     * @psalm-param array<array-key, mixed> $criteria
     * @psalm-param array<array-key, mixed> $additionalQueryParams
     *
     * @throws ExceptionInterface
     */
    public function fetchOneBy(array $criteria, array $additionalQueryParams = []): ?ApiResourceDtoInterface
    {
        $items = iterator_to_array($this->fetchBy($criteria, $additionalQueryParams));
        $item = reset($items);

        return false !== $item ? $item : null;
    }

    /**
     * @deprecated since version 0.3.0, will be removed in 1.0. Use {@see \Mtarld\ApiPlatformMsBundle\HttpRepository\AbstractMicroserviceHttpRepository::fetchOneBy()} instead.
     *
     * @psalm-param mixed $value
     * @psalm-param array<array-key, mixed> $additionalQueryParams
     *
     * @throws ExceptionInterface
     */
    public function findOneBy(string $property, $value, array $additionalQueryParams = []): ?ApiResourceDtoInterface
    {
        return $this->fetchOneBy([$property => $value], $additionalQueryParams);
    }

    /**
     * @psalm-param array<array-key, mixed> $criteria
     * @psalm-param array<array-key, mixed> $additionalQueryParams
     * @psalm-return Collection<ApiResourceDtoInterface>
     *
     * @throws ExceptionInterface
     */
    public function fetchBy(array $criteria, array $additionalQueryParams = []): Collection
    {
        return $this->requestCollection($criteria + $additionalQueryParams);
    }

    /**
     * @deprecated since version 0.3.0, will be removed in 1.0. Use {@see \Mtarld\ApiPlatformMsBundle\HttpRepository\AbstractMicroserviceHttpRepository::fetchBy()} instead.
     *
     * @param array<mixed> $values
     * @psalm-param array<array-key, mixed> $additionalQueryParams
     * @psalm-return Collection<ApiResourceDtoInterface>
     *
     * @throws ExceptionInterface
     */
    public function findBy(string $property, array $values, array $additionalQueryParams = []): Collection
    {
        return $this->fetchBy([$property => $values], $additionalQueryParams);
    }

    /**
     * @psalm-param array<array-key, mixed> $additionalQueryParams
     * @psalm-return Collection<ApiResourceDtoInterface>
     *
     * @throws ExceptionInterface
     */
    public function fetchAll(array $additionalQueryParams = []): Collection
    {
        return $this->requestCollection($additionalQueryParams);
    }

    /**
     * @deprecated since version 0.3.0, will be removed in 1.0. Use {@see \Mtarld\ApiPlatformMsBundle\HttpRepository\AbstractMicroserviceHttpRepository::fetchAll()} instead.
     *
     * @psalm-param array<array-key, mixed> $additionalQueryParams
     * @psalm-return Collection<ApiResourceDtoInterface>
     *
     * @throws ExceptionInterface
     */
    public function findAll(array $additionalQueryParams = []): Collection
    {
        return $this->fetchAll($additionalQueryParams);
    }

    /**
     * @psalm-param array<array-key, mixed> $additionalQueryParams
     *
     * @throws ResourceValidationException
     * @throws ExceptionInterface
     */
    public function create(ApiResourceDtoInterface $resource, array $additionalQueryParams = []): ApiResourceDtoInterface
    {
        try {
            $response = $this->request('POST', $this->buildUri($this->getResourceEndpoint(), $additionalQueryParams), $resource, null, 'json');

            /** @var ApiResourceDtoInterface $createdResource */
            $createdResource = $this->serializer->deserialize($response->getContent(), $this->getResourceDto(), $this->getMicroservice()->getFormat());
        } catch (ClientExceptionInterface $e) {
            if ((400 === $e->getCode()) && null !== $violations = $this->createConstraintViolationListFromResponse($e->getResponse())) {
                throw new ResourceValidationException($resource, $violations);
            }

            throw $e;
        }

        return $createdResource;
    }

    /**
     * Update a resource using PUT verb.
     *
     * @psalm-param array<array-key, mixed> $additionalQueryParams
     *
     * @throws ResourceValidationException
     * @throws ExceptionInterface
     * @throws RuntimeException
     */
    public function update(ApiResourceDtoInterface $resource, array $additionalQueryParams = []): ApiResourceDtoInterface
    {
        if (null === $iri = $resource->getIri()) {
            throw new RuntimeException('Cannot update a resource without iri');
        }

        try {
            $response = $this->request('PUT', $this->buildUri($iri, $additionalQueryParams), $resource, null, 'json');

            /** @var ApiResourceDtoInterface $updatedResource */
            return $this->serializer->deserialize($response->getContent(), $this->getResourceDto(), $this->getMicroservice()->getFormat());
        } catch (ClientExceptionInterface $e) {
            if ((400 === $e->getCode()) && null !== $violations = $this->createConstraintViolationListFromResponse($e->getResponse())) {
                throw new ResourceValidationException($resource, $violations);
            }

            throw $e;
        }
    }

    /**
     * Partially update a resource using PATCH verb.
     *
     * @psalm-param array<array-key, mixed> $additionalQueryParams
     *
     * @throws ResourceValidationException
     * @throws ExceptionInterface
     * @throws RuntimeException
     */
    public function partialUpdate(ApiResourceDtoInterface $resource, array $additionalQueryParams = []): ApiResourceDtoInterface
    {
        if (null === $iri = $resource->getIri()) {
            throw new RuntimeException('Cannot partially update a resource without iri');
        }

        try {
            $response = $this->request('PATCH', $this->buildUri($iri, $additionalQueryParams), $resource, 'application/merge-patch+json', 'json');

            /** @var ApiResourceDtoInterface $updatedResource */
            return $this->serializer->deserialize($response->getContent(), $this->getResourceDto(), $this->getMicroservice()->getFormat());
        } catch (ClientExceptionInterface $e) {
            if ((400 === $e->getCode()) && null !== $violations = $this->createConstraintViolationListFromResponse($e->getResponse())) {
                throw new ResourceValidationException($resource, $violations);
            }

            throw $e;
        }
    }

    /**
     * @psalm-param array<array-key, mixed> $additionalQueryParams
     *
     * @throws ResourceValidationException
     * @throws ExceptionInterface
     * @throws RuntimeException
     */
    public function delete(ApiResourceDtoInterface $resource, array $additionalQueryParams = []): void
    {
        if (null === $iri = $resource->getIri()) {
            throw new RuntimeException('Cannot update a resource without iri');
        }

        $response = $this->request('DELETE', $this->buildUri($iri, $additionalQueryParams));
        $statusCode = $response->getStatusCode();

        if (500 <= $statusCode) {
            throw new ServerException($response);
        }

        if (400 <= $statusCode) {
            if (null !== $violations = $this->createConstraintViolationListFromResponse($response)) {
                throw new ResourceValidationException($resource, $violations);
            }

            throw new ClientException($response);
        }

        if (300 <= $statusCode) {
            throw new RedirectionException($response);
        }
    }

    /**
     * @psalm-param array<array-key, mixed> $queryParams
     * @psalm-return Collection<ApiResourceDtoInterface>
     *
     * @throws ExceptionInterface
     */
    protected function requestCollection(array $queryParams = []): Collection
    {
        $response = $this->request('GET', $this->buildUri($this->getResourceEndpoint(), $queryParams));

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
    protected function request(string $method, string $uri, $body = null, ?string $mimeType = null, ?string $bodyFormat = null): ResponseInterface
    {
        return $this->httpClient->request($this->getMicroservice(), $method, $uri, $body, $mimeType, $bodyFormat);
    }

    private function getMicroservice(): Microservice
    {
        return $this->microservices->get($this->getMicroserviceName());
    }

    private function createConstraintViolationListFromResponse(ResponseInterface $response): ?ConstraintViolationList
    {
        try {
            /** @var ConstraintViolationList $violations */
            $violations = $this->serializer->deserialize($response->getContent(false), ConstraintViolationList::class, $this->getMicroservice()->getFormat());
        } catch (SerializerExceptionInterface $e) {
            return null;
        }

        return $violations;
    }

    private function buildUri(string $uri, array $queryParams): string
    {
        return [] !== $queryParams ? sprintf('%s?%s', $uri, http_build_query($queryParams)) : $uri;
    }
}
