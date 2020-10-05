<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\Dto;

use Mtarld\ApiPlatformMsBundle\Dto\ApiResourceDtoInterface;
use Mtarld\ApiPlatformMsBundle\Dto\ApiResourceDtoTrait;

class HairResourceDto implements ApiResourceDtoInterface
{
    use ApiResourceDtoTrait;

    /**
     * @var int
     */
    public $length;

    /**
     * @var ColorResourceDto
     */
    public $color;

    public function __construct(string $iri, int $length, ColorResourceDto $color)
    {
        $this->iri = $iri;
        $this->length = $length;
        $this->color = $color;
    }
}
