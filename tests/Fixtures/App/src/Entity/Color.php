<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(normalizationContext={"groups"={"read"}})
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class Color
{
    /**
     * @ApiProperty(identifier=true)
     * @Groups({"read"})
     */
    public int $id;

    /**
     * @Groups({"read"})
     */
    public string $hex;

    public function __construct(int $id, string $hex)
    {
        $this->id = $id;
        $this->hex = $hex;
    }
}
