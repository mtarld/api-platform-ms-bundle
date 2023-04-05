<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\Dto;

use Mtarld\ApiPlatformMsBundle\Dto\ApiResourceDtoInterface;
use Mtarld\ApiPlatformMsBundle\Dto\ApiResourceDtoTrait;

/**
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class ColorResourceDto implements ApiResourceDtoInterface
{
    use ApiResourceDtoTrait;

    public string $hex;

    public function __construct(string $iri, string $hex)
    {
        $this->iri = $iri;
        $this->hex = $hex;
    }
}
