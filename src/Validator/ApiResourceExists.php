<?php

namespace Mtarld\ApiPlatformMsBundle\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY)]
final class ApiResourceExists extends Constraint
{
    public const IRI_NOT_FOUND_ERROR = 'f0bcb756-1ca0-4611-bd68-84286ed1525e';

    protected const ERROR_NAMES = [
        self::IRI_NOT_FOUND_ERROR => 'IRI_NOT_FOUND_ERROR',
    ];

    /**
     * @param string $microservice The name of the target microservice
     * @param bool   $skipOnError  Skip validation on HTTP errors
     * @param string $regexPattern The regex pattern which an IRI must match
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function __construct(
        public string $microservice,
        public bool $skipOnError = false,
        public string $regexPattern = '/^\/[\w-]+(\/[\w-]+)+$/',
        public string $message = "'{{ iri }}' does not exist in microservice '{{ microservice }}'",
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct([], $groups, $payload);
    }
}
