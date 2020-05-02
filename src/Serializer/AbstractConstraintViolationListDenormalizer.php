<?php

namespace Mtarld\ApiPlatformMsBundle\Serializer;

use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * @internal
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
abstract class AbstractConstraintViolationListDenormalizer implements DenormalizerInterface, CacheableSupportsMethodInterface
{
    use NameConverterAwareTrait;

    abstract protected function getFormat(): string;

    abstract protected function getViolationsKey(): string;

    abstract protected function denormalizeViolation(array $data): ConstraintViolation;

    /**
     * @psalm-param class-string<ConstraintViolationList> $type
     *
     * @param string $format
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function denormalize($data, $type, $format = null, array $context = []): ConstraintViolationList
    {
        return new ConstraintViolationList(array_map(function (array $violation): ConstraintViolation {
            return $this->denormalizeViolation($violation);
        }, $data[$this->getViolationsKey()]));
    }

    /**
     * @param string $type
     * @param string $format
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return ConstraintViolationList::class === $type && $this->getFormat() === $format;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
