<?php

namespace Mtarld\ApiPlatformMsBundle\Serializer\JsonApi;

use Mtarld\ApiPlatformMsBundle\Collection\Pagination;
use Mtarld\ApiPlatformMsBundle\Serializer\AbstractCollectionDenormalizer;

/**
 * @final @internal
 */
class CollectionDenormalizer extends AbstractCollectionDenormalizer
{
    use JsonApiDenormalizerTrait;

    protected function denormalizeElements(array $data, string $enclosedType, array $context): array
    {
        return array_map(function (array $element) use ($enclosedType, $context) {
            return $this->denormalizer->denormalize(['data' => $element], $enclosedType, $this->getFormat(), $context);
        }, $data['data']);
    }

    protected function getTotalItems(array $data): int
    {
        return $data['meta']['totalItems'];
    }

    protected function getPagination(array $data): ?Pagination
    {
        return !empty($data['links']['first'] ?? null)
            ? new Pagination(
                $data['links']['self'],
                $data['links']['first'],
                $data['links']['last'],
                $data['links']['prev'] ?? null,
                $data['links']['next'] ?? null
            )
            : null
        ;
    }

    protected function isRawCollection(array $data): bool
    {
        return !array_key_exists('data', $data);
    }
}
