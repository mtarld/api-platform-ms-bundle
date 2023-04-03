<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\Dto;

/**
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class PuppyDto
{
    public int $id;

    public string $superName;

    public function __construct(int $id, string $superName)
    {
        $this->id = $id;
        $this->superName = $superName;
    }
}
