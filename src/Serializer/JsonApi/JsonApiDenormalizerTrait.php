<?php

namespace Mtarld\ApiPlatformMsBundle\Serializer\JsonApi;

/**
 * @internal
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
trait JsonApiDenormalizerTrait
{
    protected function getFormat(): string
    {
        return 'jsonapi';
    }
}
