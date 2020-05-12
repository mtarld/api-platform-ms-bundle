<?php

namespace Mtarld\ApiPlatformMsBundle\Serializer\JsonApi;

use ApiPlatform\Core\JsonApi\Serializer\ReservedAttributeNameConverter;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

/**
 * @final @internal
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ObjectDenormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface
{
    private const ALREADY_CALLED = 'jsonapi_object_denormalizer_already_called';

    use JsonApiDenormalizerTrait;
    use DenormalizerAwareTrait;

    /**
     * @param string $type
     * @param string $format
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        $context[self::ALREADY_CALLED] = true;

        if (array_key_exists('data', $data)) {
            $data = $data['data'];
        }

        if (array_key_exists('attributes', $data)) {
            $data = $data['attributes'];
        }

        foreach ($data as $key => $value) {
            if (in_array($key, ReservedAttributeNameConverter::JSON_API_RESERVED_ATTRIBUTES, true)) {
                $data[array_flip(ReservedAttributeNameConverter::JSON_API_RESERVED_ATTRIBUTES)[$key]] = $value;
                unset($data[$key]);
            }
        }

        return $this->denormalizer->denormalize($data, $type, $format, $context);
    }

    /**
     * @param string $type
     * @param string $format
     */
    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        return false === ($context[self::ALREADY_CALLED] ?? false) && $this->getFormat() === $format;
    }
}
