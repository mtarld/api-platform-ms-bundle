<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
#[ApiResource(normalizationContext: ['groups' => ['read']])]
class Color
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        #[Groups('read')]
        public int $id,

        #[Groups('read')]
        public string $hex
    ) {
    }
}
