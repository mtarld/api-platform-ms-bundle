<?php

namespace Mtarld\ApiPlatformMsBundle\Dto;

/**
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
interface ApiResourceDtoInterface
{
    public function getIri(): ?string;

    public function setIri(string $iri): void;
}
