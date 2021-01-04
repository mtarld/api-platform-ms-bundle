<?php

namespace Mtarld\ApiPlatformMsBundle\Serializer\Hal;

/**
 * @internal
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
trait HalDenormalizerTrait
{
    protected function getFormat(): string
    {
        return 'jsonhal';
    }
}
