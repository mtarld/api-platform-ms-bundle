<?php

namespace Mtarld\ApiPlatformMsBundle\Serializer\Hal;

use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @final @internal
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class ObjectDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface, CacheableSupportsMethodInterface
{
    use HalDenormalizerTrait;
    use DenormalizerAwareTrait;

    /**
     * @param mixed  $data
     * @param string $type
     * @param string $format
     *
     * @return mixed
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        return $this->denormalizer->denormalize($data, $type, 'json', $context);
    }

    /**
     * @param mixed  $data
     * @param string $type
     * @param string $format
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return $this->getFormat() === $format;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
