<?php

namespace Mtarld\ApiPlatformMsBundle\Serializer;

use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ConstraintViolationListDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    private const FORMATS = ['json', 'jsonhal'];

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
            return new ConstraintViolation($violation['title'], null, [], null, $violation['propertyPath'], null);
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
            && in_array($format, self::FORMATS, true)
            && is_array($data['violations'] ?? null)
        ;
    }
}
