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
     * @var array<string>
     *
     * @Assert\All({
     *
     *     @Assert\Type(type={"string"}),
     *
     *     @Assert\NotBlank(allowNull=false)
     * })
     */
    #[Assert\NotNull]
    public $iris;

    public function __construct(array $iris)
    {
        $this->iris = $iris;
    }
}
