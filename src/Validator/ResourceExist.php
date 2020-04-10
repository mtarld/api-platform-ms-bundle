<?php

namespace Mtarld\ApiPlatformMsBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @psalm-suppress PropertyNotSetInConstructor
 */
class ResourceExist extends Constraint
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
