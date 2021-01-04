<?php

namespace Mtarld\ApiPlatformMsBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @final @internal
 * @psalm-suppress PropertyNotSetInConstructor
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
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
