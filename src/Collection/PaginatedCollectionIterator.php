<?php

namespace Mtarld\ApiPlatformMsBundle\Collection;

use Iterator;
use Mtarld\ApiPlatformMsBundle\Exception\CollectionNotIterableException;
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

    public function __construct(GenericHttpClient $httpClient, SerializerInterface $serializer)
    {
        $this->httpClient = $httpClient;
        $this->serializer = $serializer;
    }

    /**
     * @psalm-param Collection<T> $collection
     * @psalm-return Iterator<T>
     *
     * @throws ExceptionInterface
     */
    public function iterateOver(Collection $collection): Iterator
    {
        if (null === $collection->getMicroservice()) {
            throw new CollectionNotIterableException("Collection isn't iterable because it doesn't hold microservice metadata");
        }

        foreach ($collection as $element) {
            yield $element;
        }

        if (null === $pagination = $collection->getPagination()) {
            return;
        }

        if (null === $nextPage = $pagination->getNext()) {
            return;
        }

        $nextPart = $this->getNextCollectionPart($collection, $nextPage);

        yield from $this->iterateOver($nextPart);
    }

    /**
     * @psalm-return Collection<T>
     *
     * @throws ExceptionInterface
     */
    private function getNextCollectionPart(Collection $collection, string $nextPage): Collection
    {
        /** @var Microservice $microservice */
        $microservice = $collection->getMicroservice();

        /** @var Collection $nextPart */
        $nextPart = $this->serializer->deserialize(
            $this->httpClient->request($microservice, 'GET', $nextPage)->getContent(),
            sprintf('%s<%s>', Collection::class, get_class($collection->getIterator()->current())),
            $microservice->getFormat()
        );

        return $nextPart->withMicroservice($microservice);
    }
}
