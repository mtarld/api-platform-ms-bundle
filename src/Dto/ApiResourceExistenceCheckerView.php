<?php

namespace Mtarld\ApiPlatformMsBundle\Dto;

/**
 * @final @internal
 */
class ApiResourceExistenceCheckerView
{
    /**
     * @var array
     */
    public $existences;

    /**
     * @psalm-param array<array-key, bool> $existences
     */
    public function __construct(array $existences)
    {
        $this->existences = $existences;
    }
}
