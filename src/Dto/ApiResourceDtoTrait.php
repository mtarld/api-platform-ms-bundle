<?php

namespace Mtarld\ApiPlatformMsBundle\Dto;

/**
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
trait ApiResourceDtoTrait
{
    /**
     * @var string|null
     */
    protected $iri;

    public function getIri(): ?string
    {
        return $this->iri;
    }

    public function setIri(string $iri): void
    {
        $this->iri = $iri;
    }
}
