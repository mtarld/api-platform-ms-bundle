<?php

namespace Mtarld\ApiPlatformMsBundle\Serializer\Hydra;

/**
 * @internal
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
trait HydraDenormalizerTrait
{
    protected function getFormat(): string
    {
        return 'jsonld';
    }
}
