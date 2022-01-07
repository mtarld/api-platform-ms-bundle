<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\Dto;

use Mtarld\ApiPlatformMsBundle\Dto\ApiResourceDtoInterface;
use Mtarld\ApiPlatformMsBundle\Dto\ApiResourceDtoTrait;

/**
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class PuppyResourceDto implements ApiResourceDtoInterface
{
    use ApiResourceDtoTrait;

    public function __construct(?string $iri, public string $superName, public ?ColorResourceDto $color = null, public ?array $hairs = [])
    {
        $this->iri = $iri;
    }
}
