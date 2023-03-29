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
class Hair
{
    /**
     * @var int
     *
     * @ApiProperty(identifier=true)
     */
    #[Groups(['read'])]
    public $id;

    /**
     * @var int
     */
    #[Groups(['read'])]
    public $length;

    /**
     * @var Color
     */
    #[Groups(['read'])]
    public $color;

    public function __construct(int $id, int $length, Color $color)
    {
        $this->id = $id;
        $this->length = $length;
        $this->color = $color;
    }
}
