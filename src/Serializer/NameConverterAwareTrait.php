<?php

namespace Mtarld\ApiPlatformMsBundle\Serializer;

use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

/**
 * @internal
 */
trait NameConverterAwareTrait
{
    /**
     * @var NameConverterInterface|null
     */
    protected $nameConverter;

    public function setNameConverter(?NameConverterInterface $nameConverter): void
    {
        $this->nameConverter = $nameConverter;
    }
}
