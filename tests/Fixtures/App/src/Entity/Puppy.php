<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
#[ApiResource(
    operations: [
        new Delete(uriTemplate: 'puppies/{id}'),
        new Get(uriTemplate: 'puppies/{id}'),
        new Put(uriTemplate: 'puppies/{id}'),
        new Patch(uriTemplate: 'puppies/{id}'),
        new GetCollection(uriTemplate: 'puppies', itemUriTemplate: 'puppies/{id}'),
        new Post(uriTemplate: 'puppies'),
    ],
    normalizationContext: ['groups' => 'read']
)]
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
