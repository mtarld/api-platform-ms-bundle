<?php

namespace Mtarld\ApiPlatformMsBundle\Serializer;

use Mtarld\ApiPlatformMsBundle\Collection\Collection;
use Mtarld\ApiPlatformMsBundle\Collection\Pagination;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

// Help opcache.preload discover always-needed symbols
class_exists(Collection::class);

/**
 * @internal
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
abstract class AbstractCollectionDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface, CacheableSupportsMethodInterface
{
    use DenormalizerAwareTrait;

    abstract protected function getFormat(): string;

    /**
     * @return array<object>
     */
    abstract protected function denormalizeElements(array $data, string $enclosedType, array $context): array;

    abstract protected function isRawCollection(array $data): bool;

    abstract protected function getTotalItems(array $data): int;

    abstract protected function getPagination(array $data): ?Pagination;

    /**
     * @param mixed  $data
     * @param string $type
     * @param string $format
     *
     * @return \Mtarld\ApiPlatformMsBundle\Collection\Collection<object>
     */
    public function denormalize($data, $type, $format = null, array $context = []): Collection
    {
        if ($this->isRawCollection($data)) {
            return new Collection($elements = $this->denormalizeRawElements($data, $this->getEnclosedType($type), $context), count($elements));
        }

        return new Collection(
            $this->denormalizeElements($data, $this->getEnclosedType($type), $context),
            $this->getTotalItems($data),
            $this->getPagination($data)
        );
    }

    /**
     * @param mixed  $data
     * @param string $type
     * @param string $format
     */
    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        if ($this->getFormat() !== $format) {
            return false;
        }

        $regex = sprintf('#^%s<%s>$#', preg_quote(Collection::class, '#'), '(?P<enclosedType>[a-zA-Z0-9\\\\]+)');
        $matches = [];
        preg_match($regex, $type, $matches);

        if (empty($enclosedType = $matches['enclosedType'] ?? null)) {
            return false;
        }

        return $this->denormalizer->supportsDenormalization($data, $enclosedType, $format, $context);
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }

    /**
     * @return list<object>
     */
    protected function denormalizeRawElements(array $data, string $enclosedType, array $context): array
    {
        return array_map(function (array $elementData) use ($enclosedType, $context) {
            /** @var object $rawElement */
            $rawElement = $this->denormalizer->denormalize($elementData, $enclosedType, $this->getFormat(), $context);

            return $rawElement;
        }, $data);
    }

    protected function getEnclosedType(string $type): string
    {
        $matches = [];
        preg_match('#^[a-zA-Z\\\\]+<(?P<enclosedType>[a-zA-Z0-9\\\\]+)>$#', $type, $matches);

        return $matches['enclosedType'];
    }
}
