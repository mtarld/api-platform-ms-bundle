<?php

namespace Mtarld\ApiPlatformMsBundle\Serializer\JsonApi;

use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ConstraintViolationListDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    private const FORMAT = 'jsonapi';

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
            $pointerParts = explode('/', $violation['source']['pointer']);

            return new ConstraintViolation($violation['detail'], null, [], null, end($pointerParts), null);
        }, $data['errors']);

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
            && is_array($data['errors'] ?? null)
        ;
    }
}
