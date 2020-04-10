<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\Dto;

class PuppyDto
{
    public $id;
    public $superName;

    public function __construct(int $id, string $superName)
    {
        $this->id = $id;
        $this->superName = $superName;
    }
}
