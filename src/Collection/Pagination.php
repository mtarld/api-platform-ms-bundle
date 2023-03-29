<?php

namespace Mtarld\ApiPlatformMsBundle\Collection;

/**
 * @final
 *
 * @psalm-immutable
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class Pagination
{
    public function __construct(private readonly string $current, private readonly string $first, private readonly string $last, private readonly ?string $previous, private readonly ?string $next)
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
