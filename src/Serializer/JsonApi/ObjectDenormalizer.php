<?php

namespace Mtarld\ApiPlatformMsBundle\Serializer\JsonApi;

use ApiPlatform\Core\JsonApi\Serializer\ReservedAttributeNameConverter;
use Mtarld\ApiPlatformMsBundle\Serializer\AbstractObjectDenormalizer;

/**
 * @final @internal
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ObjectDenormalizer extends AbstractObjectDenormalizer
{
    use JsonApiDenormalizerTrait;

    /**
     * @param string $type
     * @param string $format
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
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

        return parent::denormalize($data, $type, $format, $context);
    }
}
