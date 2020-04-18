<?php

namespace Mtarld\ApiPlatformMsBundle\Resource;

/**
 * @internal
 */
class ExistenceCheckerView
{
    /**
     * @var array<bool>
     */
    public $existences;

    public function __construct(array $existences)
    {
        $this->existences = $existences;
    }
}
