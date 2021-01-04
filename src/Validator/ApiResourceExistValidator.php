<?php

namespace Mtarld\ApiPlatformMsBundle\Validator;

use LogicException;
use Mtarld\ApiPlatformMsBundle\ApiResource\ExistenceChecker;
use Psr\Log\LoggerAwareTrait;
use RuntimeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Throwable;

// Help opcache.preload discover always-needed symbols
class_exists(LogicException::class);
class_exists(RuntimeException::class);

/**
 * @final
 * @psalm-suppress PropertyNotSetInConstructor
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class ApiResourceExistValidator extends ConstraintValidator
{
    use LoggerAwareTrait;

    private $existenceChecker;

    public function __construct(ExistenceChecker $existenceChecker)
    {
        $this->existenceChecker = $existenceChecker;
    }

    /**
     * @param ApiResourceExist $constraint
     * @psalm-param string|array<string>|null $value
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function validate($value, Constraint $constraint): void
    {
        if (empty($constraint->microservice)) {
            throw new LogicException(sprintf("You must specify 'microservice' attribute of '%s'", ApiResourceExist::class));
        }

        if (null === $value) {
            return;
        }

        $this->validateIris(is_array($value) ? $value : [$value], $constraint);
    }

    /**
     * @psalm-param array<string> $iris
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
        } catch (Throwable $e) {
            $this->handleExistenceCheckerHttpException($e, $constraint);
        }
    }

    private function handleExistenceCheckerHttpException(Throwable $exception, ApiResourceExist $constraint): void
    {
        $message = sprintf(
            "Unable to validate IRIs of microservice '%s': %s",
            $constraint->microservice,
            $exception->getMessage()
        );

        $this->logger->debug($message);

        if ($constraint->skipOnError) {
            return;
        }

        throw new RuntimeException($message);
    }
}
