<?php

namespace Mtarld\ApiPlatformMsBundle\Serializer\Hal;

use Mtarld\ApiPlatformMsBundle\Serializer\AbstractApiResourceDenormalizer;

/**
 * @final @internal
 */
class ApiResourceDenormalizer extends AbstractApiResourceDenormalizer
{
    use HalDenormalizerTrait;

    protected function getIri(array $data): string
    {
        return $data['_links']['self']['href'];
    }

    protected function prepareData(array $data): array
    {
        return $data;
    }
}
