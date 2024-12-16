<?php

namespace Mtarld\ApiPlatformMsBundle\Dto;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @final @internal
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class ApiResourceExistenceCheckerPayload
{
    /**
     * @param array<string> $iris
     */
    public function __construct(
        #[Assert\All([
            new Assert\Type('string'),
            new Assert\NotBlank(allowNull: false),
        ])]
        public array $iris,
    ) {
    }
}
