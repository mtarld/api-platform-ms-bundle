<?php

namespace Mtarld\ApiPlatformMsBundle\Serializer;

use Mtarld\ApiPlatformMsBundle\Dto\ApiResourceDtoInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

// Help opcache.preload discover always-needed symbols
class_exists(ApiResourceDtoInterface::class);

/**
 * @internal
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
abstract class AbstractApiResourceDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    abstract protected function getFormat(): string;

    abstract protected function getIri(array $data): string;

    abstract protected function prepareData(array $data): array;

    abstract protected function prepareEmbeddedData(array $data): array;

    /**
     * @param mixed       $data
     * @param string      $type
     * @param string|null $format
     *
     * @return mixed
     *
     * @throws ExceptionInterface
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        $iri = $this->getIri($data);
        $data = $this->prepareEmbeddedData($data);
        $data = $this->prepareData($data);
        $data['iri'] = $iri;

        return $this->denormalizer->denormalize($data, $type, $this->getFormat());
    }

    /**
     * @param mixed       $data
     * @param string      $type
     * @param string|null $format
     */
    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        return
            !isset($data['iri'])
            && $this->getFormat() === $format
            && is_a($type, ApiResourceDtoInterface::class, true);
    }

    public function getSupportedTypes(?string $format): array
    {
        return ['*' => false];
    }
}
