<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\Dto;

use Mtarld\ApiPlatformMsBundle\Dto\ApiResourceDtoInterface;
use Mtarld\ApiPlatformMsBundle\Dto\ApiResourceDtoTrait;

class PuppyResourceDto implements ApiResourceDtoInterface
{
    use ApiResourceDtoTrait;

    /**
     * @var string
     */
    public $superName;

    /**
     * @var ColorResourceDto|null
     */
    public $color;

    /**
     * @var HairResourceDto[]|null
     */
    public $hairs;

    public function __construct(string $iri, string $superName, ?ColorResourceDto $color = null, ?array $hairs = [])
    {
        $this->iri = $iri;
        $this->superName = $superName;
        $this->color = $color;
        $this->hairs = $hairs;
    }
}
