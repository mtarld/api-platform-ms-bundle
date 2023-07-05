<?php

namespace Mtarld\ApiPlatformMsBundle\Exception;

use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\RuntimeException;

/**
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class ResourceValidationException extends RuntimeException
{
    public function __construct(private readonly mixed $value, private readonly ConstraintViolationList $violations)
    {
        parent::__construct((string) $violations);
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getViolations(): ConstraintViolationList
    {
        return $this->violations;
    }
}
