<?php

namespace Mtarld\ApiPlatformMsBundle\Dto;

interface ApiResourceDtoInterface
{
    public function getIri(): string;

    public function setIri(string $iri): void;
}
