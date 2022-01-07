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
     * @param array<string, bool> $existences
     */
    public function __construct(public array $existences)
    {
    }
}
