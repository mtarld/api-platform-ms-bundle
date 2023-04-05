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

    public string $superName;

    public ?ColorResourceDto $color;

    /**
     * @var list<HairResourceDto>
     */
    public ?array $hairs;

    public function __construct(
        ?string $iri,
        string $superName,
        ?ColorResourceDto $color = null,
        ?array $hairs = []
    ) {
        $this->iri = $iri;
        $this->superName = $superName;
        $this->color = $color;
        $this->hairs = $hairs;
    }
}
