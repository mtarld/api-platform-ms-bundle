<?php

namespace Mtarld\ApiPlatformMsBundle\Serializer;

use Mtarld\ApiPlatformMsBundle\Collection\Collection;
use Mtarld\ApiPlatformMsBundle\Collection\Pagination;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @internal
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @psalm-template T as array|object
 */
abstract class AbstractCollectionDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface, CacheableSupportsMethodInterface
{
    use DenormalizerAwareTrait;

    abstract protected function getFormat(): string;

    /**
     * @psalm-return array<T>
     */
    abstract protected function denormalizeElements(array $data, string $enclosedType, array $context): array;

    abstract protected function isRawCollection(array $data): bool;

    abstract protected function getTotalItems(array $data): int;

    abstract protected function getPagination(array $data): ?Pagination;

    /**
     * @param string $type
     * @param string $format
     *
     * @psalm-return \Mtarld\ApiPlatformMsBundle\Collection\Collection<T>
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
     * @param string $type
     * @param string $format
     */
    public function supportsDenormalization($data, $type, $format = null): bool
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

        return $this->denormalizer->supportsDenormalization($data, $enclosedType, $format);
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }

    protected function denormalizeRawElements(array $data, string $enclosedType, array $context): array
    {
        return array_map(function (array $element) use ($enclosedType, $context) {
            return $this->denormalizer->denormalize($element, $enclosedType, $this->getFormat(), $context);
        }, $data);
    }

    protected function getEnclosedType(string $type): string
    {
        $matches = [];
        preg_match('#^[a-zA-Z\\\\]+<(?P<enclosedType>[a-zA-Z0-9\\\\]+)>$#', $type, $matches);

        return $matches['enclosedType'];
    }
}
