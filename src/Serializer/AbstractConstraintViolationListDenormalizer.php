<?php

namespace Mtarld\ApiPlatformMsBundle\Serializer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

// Help opcache.preload discover always-needed symbols
class_exists(ConstraintViolationList::class);

/**
 * @internal
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
abstract class AbstractConstraintViolationListDenormalizer implements DenormalizerInterface
{
    use NameConverterAwareTrait;

    abstract protected function getFormat(): string;

    abstract protected function getViolationsKey(): string;

    abstract protected function denormalizeViolation(array $data): ConstraintViolation;

    /**
     * @param class-string<ConstraintViolationList> $type
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    #[\Override]
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): ConstraintViolationList
    {
        return new ConstraintViolationList(array_map(fn (array $violation): ConstraintViolation => $this->denormalizeViolation($violation), $data[$this->getViolationsKey()]));
    }

    #[\Override]
    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return is_array($data) && array_key_exists($this->getViolationsKey(), $data);
    }

    public function getSupportedTypes(?string $format): array
    {
        return $this->getFormat() === $format ? [ConstraintViolationList::class => false] : [];
    }
}
