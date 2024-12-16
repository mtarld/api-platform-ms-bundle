<?php

namespace Mtarld\ApiPlatformMsBundle\Serializer\Hydra;

use Mtarld\ApiPlatformMsBundle\Collection\Pagination;
use Mtarld\ApiPlatformMsBundle\Serializer\AbstractCollectionDenormalizer;

// Help opcache.preload discover always-needed symbols
class_exists(Pagination::class);

/**
 * @final @internal
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
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
        }, $data['hydra:member'] ?? $data['member']);
    }

    protected function getTotalItems(array $data): int
    {
        return $data['hydra:totalItems'] ?? $data['totalItems'];
    }

    protected function getPagination(array $data): ?Pagination
    {
        $view = $data['hydra:view'] ?? $data['view'] ?? [];

        return array_key_exists('hydra:first', $view) || array_key_exists('first', $view)
            ? new Pagination(
                $view['@id'],
                $view['hydra:first'] ?? $view['first'],
                $view['hydra:last'] ?? $view['last'],
                $view['hydra:previous'] ?? $view['previous'] ?? null,
                $view['hydra:next'] ?? $view['next'] ?? null
            )
            : null;
    }

    protected function isRawCollection(array $data): bool
    {
        return !array_key_exists('@type', $data);
    }
}
