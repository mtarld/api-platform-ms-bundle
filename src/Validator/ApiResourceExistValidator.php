<?php

namespace Mtarld\ApiPlatformMsBundle\Validator;

use Mtarld\ApiPlatformMsBundle\ApiResource\ExistenceChecker;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Serializer\Exception\ExceptionInterface as SerializerExceptionInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface as HttpClientExceptionInterface;

// Help opcache.preload discover always-needed symbols
class_exists(\LogicException::class);
class_exists(\RuntimeException::class);

/**
 * @final
 *
 * @psalm-suppress PropertyNotSetInConstructor
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class ApiResourceExistValidator extends ConstraintValidator
{
    use LoggerAwareTrait;

    public function __construct(private readonly ExistenceChecker $existenceChecker)
    {
    }

    /**
     * @psalm-param string|list<string>|null $value
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ApiResourceExist) {
            throw new UnexpectedTypeException($constraint, ApiResourceExist::class);
        }

        if (null === $value) {
            return;
        }

        $this->validateIris((array) $value, $constraint);
    }

    /**
     * @param list<string> $iris
     */
    private function validateIris(array $iris, ApiResourceExist $constraint): void
    {
        try {
            $checkedIris = $this->existenceChecker->getExistenceStatuses($constraint->microservice, $iris);

            foreach ($checkedIris as $iri => $valid) {
                if (false === $valid) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ iri }}', $iri)
                        ->setParameter('{{ microservice }}', $constraint->microservice)
                        ->addViolation()
                    ;
                }
            }
        } catch (HttpClientExceptionInterface|SerializerExceptionInterface $e) {
            $this->handleExistenceCheckerHttpException($e, $constraint);
        }
    }

    private function handleExistenceCheckerHttpException(\Throwable $exception, ApiResourceExist $constraint): void
    {
        $message = sprintf(
            "Unable to validate IRIs of microservice '%s': %s",
            $constraint->microservice,
            $exception->getMessage()
        );

        if (null !== $this->logger) {
            $this->logger->debug($message);
        }

        if ($constraint->skipOnError) {
            return;
        }

        throw new \RuntimeException($message);
    }
}
