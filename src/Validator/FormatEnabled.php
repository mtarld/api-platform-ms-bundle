<?php

namespace Mtarld\ApiPlatformMsBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @final @internal
 * @Annotation
 * @psalm-suppress PropertyNotSetInConstructor
 */
class FormatEnabled extends Constraint
{
    /**
     * @var string
     *
     * Violation message
     */
    public $message = "'{{ format }}' format is not enabled.";
}
