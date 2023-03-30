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
    #[ApiProperty(identifier: true)]
    #[Groups(['read'])]
    public int $id;

    #[Groups(['read'])]
    public string $superName;

    #[Groups(['read'])]
    public ?Color $color;

    /**
     * @var list<Hair>|null
     */
    #[Groups(['read'])]
    public ?array $hairs;

    public function __construct(
        int $id,
        string $superName,
        ?Color $color = null,
        ?array $hairs = []
    ) {
        $this->id = $id;
        $this->superName = $superName;
        $this->color = $color;
        $this->hairs = $hairs;
    }
}
