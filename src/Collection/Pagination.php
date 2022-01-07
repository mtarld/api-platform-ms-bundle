<?php

namespace Mtarld\ApiPlatformMsBundle\Collection;

/**
 * @final
 * @psalm-immutable
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class Pagination
{
    public function __construct(private string  $current,
                                private string  $first,
                                private string  $last,
                                private ?string $previous,
                                private ?string $next)
    {
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
