<?php

namespace Mtarld\ApiPlatformMsBundle\Serializer\JsonApi;

trait JsonApiDenormalizerTrait
{
    protected function getFormat(): string
    {
        return 'jsonapi';
    }
}
