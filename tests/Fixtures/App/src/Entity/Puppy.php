<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ApiResource()
 */
class Puppy
{
    /**
     * @ApiProperty(identifier=true)
     */
    public $id;

    public $superName;

    public function __construct(int $id, string $superName)
    {
        $this->id = $id;
        $this->superName = $superName;
    }
}
