<?php

namespace Mtarld\ApiPlatformMsBundle\Dto;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @final @internal
 */
class ApiResourceExistenceCheckerPayload
{
    /**
     * @var array<string>
     *
     * @Assert\NotNull
     * @Assert\All({
     *     @Assert\Type(type={"string"}),
     *     @Assert\NotBlank(allowNull=false)
     * })
     */
    public $iris;

    public function __construct(array $iris)
    {
        $this->iris = $iris;
    }
}
