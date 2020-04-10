<?php

namespace Mtarld\ApiPlatformMsBundle\Serializer\JsonApi;

use Mtarld\ApiPlatformMsBundle\Serializer\AbstractApiResourceDenormalizer;

/**
 * @final @internal
 */
class ApiResourceDenormalizer extends AbstractApiResourceDenormalizer
{
    use JsonApiDenormalizerTrait;

    protected function getIri(array $data): string
    {
        return $data['data']['id'];
    }

    protected function prepareData(array $data): array
    {
        return $data['data']['attributes'];
    }
}
