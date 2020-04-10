<?php

namespace Mtarld\ApiPlatformMsBundle\Collection;

use Iterator;
use Mtarld\ApiPlatformMsBundle\HttpClient\GenericHttpClient;
use Mtarld\ApiPlatformMsBundle\Microservice\Microservice;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;

/**
 * @final
 * @psalm-template T of object
 */
class PaginatedCollectionIterator
{
    private $httpClient;
    private $serializer;

    /**
     * @var string|null
     */
    private $elementType;

    public function __construct(GenericHttpClient $httpClient, SerializerInterface $serializer)
    {
        $this->httpClient = $httpClient;
        $this->serializer = $serializer;
    }

    /**
     * @psalm-param Collection<T> $collection
     * @psalm-return Iterator<T>
     */
    public function iterateOver(Collection $collection, Microservice $microservice): Iterator
    {
        foreach ($collection as $element) {
            yield $element;
        }

        if (null === $pagination = $collection->getPagination()) {
            return;
        }

        if (null === $nextPage = $pagination->getNext()) {
            return;
        }

        $nextPart = $this->getNextCollectionPart(
            $microservice,
            $nextPage,
            $this->elementType = $this->elementType ?? get_class($collection->getIterator()->current())
        );

        yield from $this->iterateOver($nextPart, $microservice);
    }

    /**
     * @psalm-return Collection<T>
     *
     * @throws ExceptionInterface
     */
    private function getNextCollectionPart(Microservice $microservice, string $nextPage, string $elementType): Collection
    {
        /** @var Collection $nextPart */
        $nextPart = $this->serializer->deserialize(
            $this->httpClient->request($microservice, 'GET', $nextPage)->getContent(),
            Collection::class.'<'.$elementType.'>',
            $microservice->getFormat()
        );

        return $nextPart;
    }
}
