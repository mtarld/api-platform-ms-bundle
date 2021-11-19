<?php

namespace Mtarld\ApiPlatformMsBundle\Dto;

/**
 * @final @internal
 * test pipeline
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class ApiResourceExistenceCheckerView
{
    /**
     * @var array<string, bool>
     */
    public $existences;

    /**
     * @param array<string, bool> $existences
     */
    public function __construct(array $existences)
    {
        $this->existences = $existences;
    }
}
