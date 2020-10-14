<?php

namespace Mtarld\ApiPlatformMsBundle\Serializer\Hydra;

use Mtarld\ApiPlatformMsBundle\Collection\Pagination;
use Mtarld\ApiPlatformMsBundle\Serializer\AbstractCollectionDenormalizer;

// Help opcache.preload discover always-needed symbols
class_exists(Pagination::class);

/**
 * @final @internal
 */
class CollectionDenormalizer extends AbstractCollectionDenormalizer
{
    use HydraDenormalizerTrait;

    protected function denormalizeElements(array $data, string $enclosedType, array $context): array
    {
        return array_map(function (array $elementData) use ($enclosedType, $context) {
            /** @var object $element */
            $element = $this->denormalizer->denormalize($elementData, $enclosedType, $this->getFormat(), $context);

            return $element;
        }, $data['hydra:member']);
    }

    protected function getTotalItems(array $data): int
    {
        return $data['hydra:totalItems'];
    }

    protected function getPagination(array $data): ?Pagination
    {
        return array_key_exists('hydra:first', $view = $data['hydra:view'] ?? [])
            ? new Pagination(
                $view['@id'],
                $view['hydra:first'],
                $view['hydra:last'],
                $view['hydra:previous'] ?? null,
                $view['hydra:next'] ?? null
            )
            : null
        ;
    }

    protected function isRawCollection(array $data): bool
    {
        return !array_key_exists('@type', $data);
    }
}
