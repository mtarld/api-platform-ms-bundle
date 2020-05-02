<?php

namespace Mtarld\ApiPlatformMsBundle\Collection;

use ArrayIterator;
use Countable;
use Iterator;
use IteratorAggregate;

/**
 * @final
 * @psalm-immutable
 * @psalm-template T of object
 */
class Collection implements IteratorAggregate, Countable
{
    private $elements;
    private $count;
    private $pagination;

    /**
     * @psalm-param array<T> $elements
     */
    public function __construct(
        array $elements,
        int $count,
        ?Pagination $pagination = null
    ) {
        $this->elements = $elements;
        $this->count = $count;
        $this->pagination = $pagination;
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
     * @psalm-return Iterator<T>
     */
    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->elements);
    }

    public function count(): int
    {
        return $this->count;
    }
}
