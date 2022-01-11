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

    public function __construct(protected string $iri,
                                public string $hex)
    {
    }
}
