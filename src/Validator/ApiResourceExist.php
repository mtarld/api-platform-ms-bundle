<?php

namespace Mtarld\ApiPlatformMsBundle\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class ApiResourceExist extends Constraint
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

    /**
     * @param string|array<mixed> $microservice The target microservice or a set of options
     * @param array<string>       $groups
     * @param array<mixed>        $options
     *
     * @psalm-suppress DocblockTypeContradiction
     */
    public function __construct(
        $microservice = null,
        ?bool $skipOnError = null,
        ?string $message = null,
        ?array $groups = null,
        mixed $payload = null,
        array $options = []
    ) {
        is_array($microservice) ? $options = array_merge($microservice, $options) : $options['value'] = $microservice;

        parent::__construct($options, $groups, $payload);

        if (!is_string($this->microservice)) {
            throw new \TypeError(sprintf('"%s()": Expected argument $microservice to be a string, got "%s".', __METHOD__, get_debug_type($this->microservice)));
        }

        $this->message = $message ?? $this->message;
        $this->skipOnError = $skipOnError ?? $this->skipOnError;
    }

    public function getDefaultOption(): string
    {
        return 'microservice';
    }

    public function getRequiredOptions(): array
    {
        return ['microservice'];
    }
}
