<?php

namespace Mtarld\ApiPlatformMsBundle\Serializer\Hydra;

use Mtarld\ApiPlatformMsBundle\Serializer\AbstractConstraintViolationListDenormalizer;
use Symfony\Component\Validator\ConstraintViolation;

// Help opcache.preload discover always-needed symbols
class_exists(ConstraintViolation::class);

/**
 * @final @internal
 */
class ConstraintViolationListDenormalizer extends AbstractConstraintViolationListDenormalizer
{
    use HydraDenormalizerTrait;

    protected function getViolationsKey(): string
    {
        return 'violations';
    }

    protected function denormalizeViolation(array $data): ConstraintViolation
    {
        return new ConstraintViolation(
            $data['message'],
            null,
            [],
            null,
            $this->nameConverter ? $this->nameConverter->denormalize($data['propertyPath']) : $data['propertyPath'],
            null
        );
    }
}
