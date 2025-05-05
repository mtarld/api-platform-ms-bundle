<?php

namespace Mtarld\ApiPlatformMsBundle\Validator;

use Mtarld\ApiPlatformMsBundle\ApiResource\ExistenceVerifier;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class ApiResourceExistsValidator extends ConstraintValidator
{
    use LoggerAwareTrait;

    public function __construct(
        private readonly ExistenceVerifier $existenceVerifier,
    ) {
    }

    #[\Override]
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ApiResourceExists) {
            throw new UnexpectedTypeException($constraint, ApiResourceExists::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        $currentViolations = count($this->context->getViolations());

        $this->checkPreconstraints($value, $constraint);

        if ($currentViolations === count($this->context->getViolations())) {
            try {
                if (!$this->existenceVerifier->verify($constraint->microservice, $value)) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ iri }}', $value)
                        ->setParameter('{{ microservice }}', $constraint->microservice)
                        ->setCode(ApiResourceExists::IRI_NOT_FOUND_ERROR)
                        ->addViolation()
                    ;
                }
            } catch (ExceptionInterface $e) {
                $this->handleHttpException($e, $constraint);
            }
        }
    }

    private function checkPreconstraints(mixed $value, ApiResourceExists $constraint): void
    {
        $constraints = [
            new Assert\Regex(pattern: $constraint->regexPattern),
        ];
        $validator = $this->context->getValidator()->inContext($this->context);

        $validator->validate($value, $constraints);
    }

    private function handleHttpException(ExceptionInterface $exception, ApiResourceExists $constraint): void
    {
        $message = sprintf(
            "Unable to validate IRIs of microservice '%s': %s",
            $constraint->microservice,
            $exception->getMessage()
        );

        $this->logger?->debug($message);

        if ($constraint->skipOnError) {
            return;
        }

        throw new \RuntimeException($message);
    }
}
