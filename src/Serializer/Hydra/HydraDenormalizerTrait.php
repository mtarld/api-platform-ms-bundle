<?php

namespace Mtarld\ApiPlatformMsBundle\Serializer\Hydra;

trait HydraDenormalizerTrait
{
    protected function getFormat(): string
    {
        return 'jsonld';
    }
}
