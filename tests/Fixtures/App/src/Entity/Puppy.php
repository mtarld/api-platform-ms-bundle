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
class Puppy
{
    /**
     * @var int
     *
     * @ApiProperty(identifier=true)
     */
    #[Groups(['read'])]
    public $id;

    /**
     * @var string
     */
    #[Groups(['read'])]
    public $superName;

    /**
     * @var Color|null
     */
    #[Groups(['read'])]
    public $color;

    /**
     * @var Hair[]|null
     */
    #[Groups(['read'])]
    public $hairs;

    public function __construct(int $id, string $superName, ?Color $color = null, ?array $hairs = [])
    {
        $this->id = $id;
        $this->superName = $superName;
        $this->color = $color;
        $this->hairs = $hairs;
    }
}
