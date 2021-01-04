<?php

namespace Mtarld\ApiPlatformMsBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @final
 * @psalm-suppress PropertyNotSetInConstructor
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class ApiResourceExist extends Constraint
{
    /**
     * @var string
     *
     * Violation message
     */
    public $message = "'{{ iri }}' does not exist in microservice '{{ microservice }}'.";

    /**
     * @var string
     *
     * Microservice name
     */
    public $microservice;

    /**
     * @var bool
     *
     * Skip validation on http errors
     */
    public $skipOnError = false;

    public function getDefaultOption(): string
    {
        return 'microservice';
    }
}
