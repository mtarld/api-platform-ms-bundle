<?php

namespace Mtarld\ApiPlatformMsBundle\Serializer;

use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

/**
 * @internal
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
trait NameConverterAwareTrait
{
    /**
     * @var NameConverterInterface|null
     */
    protected ?NameConverterInterface $nameConverter;

    public function setNameConverter(?NameConverterInterface $nameConverter): void
    {
        $this->nameConverter = $nameConverter;
    }
}
