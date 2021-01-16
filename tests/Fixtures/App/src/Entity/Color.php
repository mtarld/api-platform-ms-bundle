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
     * @var int
     *
     * @ApiProperty(identifier=true)
     * @Groups({"read"})
     */
    public $id;

    /**
     * @var string
     *
     * @Groups({"read"})
     */
    public $hex;

    public function __construct(int $id, string $hex)
    {
        $this->id = $id;
        $this->hex = $hex;
    }
}
