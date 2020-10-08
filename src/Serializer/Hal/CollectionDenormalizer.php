<?php

namespace Mtarld\ApiPlatformMsBundle\Serializer\Hal;

use Mtarld\ApiPlatformMsBundle\Collection\Pagination;
use Mtarld\ApiPlatformMsBundle\Serializer\AbstractCollectionDenormalizer;

/**
 * @final @internal
 */
class CollectionDenormalizer extends AbstractCollectionDenormalizer
{
    use HalDenormalizerTrait;

    /**
     * @return array<object>
     */
    protected function denormalizeElements(array $data, string $enclosedType, array $context): array
    {
        return array_map(function (array $elementData) use ($enclosedType, $context) {
            /** @var object $element */
            $element = $this->denormalizer->denormalize($elementData, $enclosedType, $this->getFormat(), $context);

            return $element;
        }, $data['_embedded']['item']);
    }

    protected function getTotalItems(array $data): int
    {
        return $data['totalItems'];
    }

    protected function getPagination(array $data): ?Pagination
    {
        return array_key_exists('first', $links = $data['_links'])
            ? new Pagination(
                $links['self']['href'],
                $links['first']['href'],
                $links['last']['href'],
                $links['prev']['href'] ?? null,
                $links['next']['href'] ?? null
            )
            : null
        ;
    }

    protected function isRawCollection(array $data): bool
    {
        return !array_key_exists('_embedded', $data);
    }
}
