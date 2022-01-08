<?php

namespace Mtarld\ApiPlatformMsBundle\Collection;

use ArrayIterator;
use Countable;
use Iterator;
use IteratorAggregate;
use Mtarld\ApiPlatformMsBundle\Microservice\Microservice;

// Help opcache.preload discover always-needed symbols
class_exists(ArrayIterator::class);
class_exists(Pagination::class);

/**
 * @final
 * @template T of object
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class Collection implements IteratorAggregate, Countable
{
    private ?Microservice $microservice;

    /**
     * @param list<T> $elements
     */
    public function __construct(
        private array $elements,
        private int $count,
        private ?Pagination $pagination = null
    ) {
        $this->microservice = null;
    }

    public function getPagination(): ?Pagination
    {
        return $this->pagination;
    }

    public function hasPagination(): bool
    {
        return $this->pagination instanceof Pagination;
    }

    /**
     * @return Iterator<T>
     */
    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->elements);
    }

    public function count(): int
    {
        return $this->count;
    }

    /**
     * @internal
     *
     * @return Collection<T>
     */
    public function withMicroservice(Microservice $microservice): self
    {
        $collection = clone $this;
        $collection->microservice = $microservice;

        return $collection;
    }

    /**
     * @internal
     */
    public function getMicroservice(): ?Microservice
    {
        return $this->microservice;
    }
}
