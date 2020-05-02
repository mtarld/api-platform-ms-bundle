<?php

namespace Mtarld\ApiPlatformMsBundle\Serializer\Hydra;

use Mtarld\ApiPlatformMsBundle\Serializer\AbstractObjectDenormalizer;

/**
 * @final @internal
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ObjectDenormalizer extends AbstractObjectDenormalizer
{
    use HydraDenormalizerTrait;

    /**
     * @param string $type
     * @param string $format
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        // Prevent Symfony\Component\Cache\CacheItem cache key exception
        foreach (array_keys($data) as $key) {
            if (0 === strpos($key, '@')) {
                unset($data[$key]);
            }
        }

        return $this->denormalizer->denormalize($data, $type, 'json');
    }
}
