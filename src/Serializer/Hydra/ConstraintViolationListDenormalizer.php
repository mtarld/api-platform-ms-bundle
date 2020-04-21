<?php

namespace Mtarld\ApiPlatformMsBundle\Serializer\Hydra;

use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ConstraintViolationListDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    private const FORMAT = 'jsonld';

    use DenormalizerAwareTrait;

    /**
     * @param string $type
     * @param string $format
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function denormalize($data, $type, $format = null, array $context = []): ConstraintViolationList
    {
        $violations = array_map(static function (array $violation): ConstraintViolation {
            return new ConstraintViolation($violation['message'], null, [], null, $violation['propertyPath'], null);
        }, $data['violations']);

        return new ConstraintViolationList($violations);
    }

    /**
     * @param string $type
     * @param string $format
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return ConstraintViolationList::class === $type
            && self::FORMAT === $format
            && 'ConstraintViolationList' === ($data['@type'] ?? null)
        ;
    }
}
