<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\Dto;

use Mtarld\ApiPlatformMsBundle\Dto\ApiResourceDtoInterface;
use Mtarld\ApiPlatformMsBundle\Dto\ApiResourceDtoTrait;

class PuppyResourceDto implements ApiResourceDtoInterface
{
    use ApiResourceDtoTrait;

    public $id;
    public $superName;

    public function __construct(string $iri, int $id, string $superName)
    {
        $this->iri = $iri;
        $this->id = $id;
        $this->superName = $superName;
    }
}
