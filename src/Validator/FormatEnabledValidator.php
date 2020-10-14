<?php

namespace Mtarld\ApiPlatformMsBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @final @internal
 * @psalm-suppress PropertyNotSetInConstructor
 */
class FormatEnabledValidator extends ConstraintValidator
{
    private $enabledFormats;

    public function __construct(array $enabledFormats)
    {
        $this->enabledFormats = $enabledFormats;
    }

    /**
     * @param FormatEnabled $constraint
     * @psalm-param string $value
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!in_array($value, $this->enabledFormats, true)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ format }}', $value)
                ->addViolation()
            ;
        }
    }
}
