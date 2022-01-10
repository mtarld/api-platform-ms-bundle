<?php

namespace Mtarld\ApiPlatformMsBundle\Collection;

use Iterator;
use Mtarld\ApiPlatformMsBundle\Exception\CollectionNotIterableException;
use Mtarld\ApiPlatformMsBundle\HttpClient\GenericHttpClient;
use Mtarld\ApiPlatformMsBundle\HttpClient\ReplaceableHttpClientInterface;
use Mtarld\ApiPlatformMsBundle\HttpClient\ReplaceableHttpClientTrait;
use Mtarld\ApiPlatformMsBundle\Microservice\Microservice;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

// Help opcache.preload discover always-needed symbols
class_exists(Collection::class);
class_exists(CollectionNotIterableException::class);

/**
 * @final
 * @template T of object
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class PaginatedCollectionIterator implements ReplaceableHttpClientInterface
{
    use ReplaceableHttpClientTrait;

    public function __construct(private GenericHttpClient $httpClient,
                                private SerializerInterface $serializer)
    {
    }

    /**
     * @param Collection<T> $collection
     *
     * @return Iterator<Iterator<T>>
     *
     * @throws ExceptionInterface
     */
    public function iteratePages(Collection $collection): Iterator
    {
        if (null === $collection->getMicroservice()) {
            throw new CollectionNotIterableException("Collection isn't iterable because it doesn't hold microservice metadata");
        }

        yield $collection;

        if (null === $pagination = $collection->getPagination()) {
            return;
        }

        if (null === $nextPage = $pagination->getNext()) {
            return;
        }

        $nextPart = $this->getNextCollectionPart($collection, $nextPage);

        yield from $this->iteratePages($nextPart);
    }

    /**
     * @param Collection<T> $collection
     *
     * @return Iterator<T>
     *
     * @throws ExceptionInterface
     */
    public function iterateItems(Collection $collection): Iterator
    {
        foreach ($this->iteratePages($collection) as $page) {
            yield from $page;
        }
    }

    /**
     * @return Collection<T>
     *
     * @throws ExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function getNextCollectionPart(Collection $collection, string $nextPage): Collection
    {
        /** @var Microservice $microservice */
        $microservice = $collection->getMicroservice();

        /** @var Collection<T> $nextPart */
        $nextPart = $this->serializer->deserialize(
            $this->httpClient->request($microservice, 'GET', $nextPage)->getContent(),
            sprintf('%s<%s>', Collection::class, get_class($collection->getIterator()->current())),
            $microservice->getFormat()
        );

        return $nextPart->withMicroservice($microservice);
    }
}
