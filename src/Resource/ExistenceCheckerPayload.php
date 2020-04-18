<?php

namespace Mtarld\ApiPlatformMsBundle\Resource;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @internal
 */
class ExistenceCheckerPayload
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
