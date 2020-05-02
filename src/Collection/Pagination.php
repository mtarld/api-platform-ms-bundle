<?php

namespace Mtarld\ApiPlatformMsBundle\Collection;

/**
 * @final
 *
 * @psalm-immutable
 */
class Pagination
{
    private $current;
    private $first;
    private $last;
    private $previous;
    private $next;

    public function __construct(string $current, string $first, string $last, ?string $previous, ?string $next)
    {
        $this->current = $current;
        $this->first = $first;
        $this->last = $last;
        $this->previous = $previous;
        $this->next = $next;
    }

    public function getCurrent(): string
    {
        return $this->current;
    }

    public function getFirst(): string
    {
        return $this->first;
    }

    public function getLast(): string
    {
        return $this->last;
    }

    public function getPrevious(): ?string
    {
        return $this->previous;
    }

    public function getNext(): ?string
    {
        return $this->next;
    }
}
