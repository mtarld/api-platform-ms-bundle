<?php

namespace Mtarld\ApiPlatformMsBundle\Serializer\Hal;

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
class ObjectDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use HalDenormalizerTrait;
    use DenormalizerAwareTrait;

    #[\Override]
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): mixed
    {
        return $this->denormalizer->denormalize($data, $type, 'json', $context);
    }

    #[\Override]
    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return $this->getFormat() === $format;
    }

    public function getSupportedTypes(?string $format): array
    {
        return ['*' => true];
    }
}
