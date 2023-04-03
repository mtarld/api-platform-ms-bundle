<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\Dto;

use Mtarld\ApiPlatformMsBundle\Dto\ApiResourceDtoInterface;
use Mtarld\ApiPlatformMsBundle\Dto\ApiResourceDtoTrait;

/**
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class HairResourceDto implements ApiResourceDtoInterface
{
    use ApiResourceDtoTrait;

    public int $length;

    public ColorResourceDto $color;

    public function __construct(string $iri, int $length, ColorResourceDto $color)
    {
        $this->iri = $iri;
        $this->length = $length;
        $this->color = $color;
    }
}
