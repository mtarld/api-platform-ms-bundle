<?php

namespace Mtarld\ApiPlatformMsBundle\Serializer\Hal;

trait HalDenormalizerTrait
{
    protected function getFormat(): string
    {
        return 'jsonhal';
    }
}
