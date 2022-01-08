<?php

namespace Mtarld\ApiPlatformMsBundle\Serializer\JsonApi;

use Mtarld\ApiPlatformMsBundle\Collection\Pagination;
use Mtarld\ApiPlatformMsBundle\Serializer\AbstractCollectionDenormalizer;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

// Help opcache.preload discover always-needed symbols
class_exists(Pagination::class);

/**
 * @final @internal
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class CollectionDenormalizer extends AbstractCollectionDenormalizer
{
    use JsonApiDenormalizerTrait;

    /**
     * @return array<object>
     *
     * @throws ExceptionInterface
     */
    protected function denormalizeElements(array $data, string $enclosedType, array $context): array
    {
        return array_map(function (array $elementData) use ($enclosedType, $context) {
            /** @var object $element */
            $element = $this->denormalizer->denormalize(['data' => $elementData], $enclosedType, $this->getFormat(), $context);

            return $element;
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
