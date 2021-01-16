<?php

namespace Mtarld\ApiPlatformMsBundle\Serializer\Hydra;

use Mtarld\ApiPlatformMsBundle\Serializer\AbstractApiResourceDenormalizer;

/**
 * @final @internal
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class ApiResourceDenormalizer extends AbstractApiResourceDenormalizer
{
    use HydraDenormalizerTrait;

    protected function getIri(array $data): string
    {
        return $data['@id'];
    }

    protected function prepareData(array $data): array
    {
        return $data;
    }

    protected function prepareEmbeddedData(array $data): array
    {
        return $data;
    }
}
