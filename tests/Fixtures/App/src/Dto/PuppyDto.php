<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\Dto;

class PuppyDto
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $superName;

    public function __construct(int $id, string $superName)
    {
        $this->id = $id;
        $this->superName = $superName;
    }
}
