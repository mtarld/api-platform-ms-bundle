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

    public function __construct(string $iri, public int $length, public ColorResourceDto $color)
    {
        $this->iri = $iri;
    }
}
