<?php

namespace Mtarld\ApiPlatformMsBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @final @internal
 * @psalm-suppress PropertyNotSetInConstructor
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class PatchFormatEnabledValidator extends ConstraintValidator
{
    private $enabledPatchFormats;

    public function __construct(array $enabledPatchFormats)
    {
        $this->enabledPatchFormats = $enabledPatchFormats;
    }

    /**
     * @param FormatEnabled $constraint
     * @psalm-param string $value
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!in_array($value, $this->enabledPatchFormats, true)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ patch_format }}', $value)
                ->addViolation()
            ;
        }
    }
}
