<?php

namespace Mtarld\ApiPlatformMsBundle\Serializer\JsonApi;

use Mtarld\ApiPlatformMsBundle\Serializer\AbstractConstraintViolationListDenormalizer;
use Symfony\Component\Validator\ConstraintViolation;

/**
 * @final @internal
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
        $pointerParts = explode('/', $data['source']['pointer']);

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
