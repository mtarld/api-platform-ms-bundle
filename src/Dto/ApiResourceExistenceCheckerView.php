<?php

namespace Mtarld\ApiPlatformMsBundle\Dto;

/**
 * @final @internal
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class ApiResourceExistenceCheckerView
{
    /**
     * @var array<string, bool>
     */
    public readonly array $existences;

    /**
     * @param array<string, bool> $existences
     */
    public function __construct(array $existences)
    {
        $this->existences = $existences;
    }
}
