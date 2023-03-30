<?php

namespace Mtarld\ApiPlatformMsBundle\Dto;

use phpDocumentor\Reflection\Type;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @final @internal
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class ApiResourceExistenceCheckerPayload
{
    /**
     * @var array<string>
     */
    #[Assert\All(
        [
            new Assert\Type('string'),
            new Assert\NotBlank(allowNull: false),
        ]
    )]
    #[Assert\NotNull]
    public array $iris;

    public function __construct(array $iris)
    {
        $this->iris = $iris;
    }
}
