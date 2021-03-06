<?php

namespace Mtarld\ApiPlatformMsBundle\Serializer\Hal;

use Mtarld\ApiPlatformMsBundle\Serializer\AbstractConstraintViolationListDenormalizer;
use Symfony\Component\Validator\ConstraintViolation;

// Help opcache.preload discover always-needed symbols
class_exists(ConstraintViolation::class);

/**
 * @final @internal
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class ConstraintViolationListDenormalizer extends AbstractConstraintViolationListDenormalizer
{
    use HalDenormalizerTrait;

    protected function getViolationsKey(): string
    {
        return 'violations';
    }

    protected function denormalizeViolation(array $data): ConstraintViolation
    {
        return new ConstraintViolation(
            $data['title'],
            null,
            [],
            null,
            $this->nameConverter ? $this->nameConverter->denormalize($data['propertyPath']) : $data['propertyPath'],
            null
        );
    }
}
