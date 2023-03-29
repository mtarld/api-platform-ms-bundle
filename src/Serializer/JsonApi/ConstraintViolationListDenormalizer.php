<?php

namespace Mtarld\ApiPlatformMsBundle\Serializer\JsonApi;

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
    use JsonApiDenormalizerTrait;

    protected function getViolationsKey(): string
    {
        return 'errors';
    }

    protected function denormalizeViolation(array $data): ConstraintViolation
    {
        $pointerParts = explode('/', (string) $data['source']['pointer']);

        return new ConstraintViolation(
            $data['detail'],
            null,
            [],
            null,
            $this->nameConverter ? $this->nameConverter->denormalize(end($pointerParts)) : end($pointerParts),
            null
        );
    }
}
