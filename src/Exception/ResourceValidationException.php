<?php

namespace Mtarld\ApiPlatformMsBundle\Exception;

use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\RuntimeException;

class ResourceValidationException extends RuntimeException
{
    /**
     * @var ConstraintViolationList
     */
    private $violations;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @param mixed $value
     */
    public function __construct($value, ConstraintViolationList $violations)
    {
        $this->violations = $violations;
        $this->value = $value;

        parent::__construct((string) $violations);
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public function getViolations(): ConstraintViolationList
    {
        return $this->violations;
    }
}
