<?php

namespace Mtarld\ApiPlatformMsBundle\Serializer;

use Mtarld\ApiPlatformMsBundle\Dto\ApiResourceDtoInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @internal
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
abstract class AbstractApiResourceDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    abstract protected function getFormat(): string;

    abstract protected function getIri(array $data): string;

    abstract protected function prepareData(array $data): array;

    /**
     * @param string $type
     * @param string $format
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        $context[static::class.'_called'] = true;
        $iri = $this->getIri($data);

        $data = $this->prepareData($data);
        $data['iri'] = $iri;

        return $this->denormalizer->denormalize($data, $type, $this->getFormat(), $context);
    }

    /**
     * @param string $type
     * @param string $format
     */
    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        return
            !($context[static::class.'_called'] ?? false)
            && $this->getFormat() === $format
            && is_a($type, ApiResourceDtoInterface::class, true)
        ;
    }
}
