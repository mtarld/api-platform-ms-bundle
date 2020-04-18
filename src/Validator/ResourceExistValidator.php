<?php

namespace Mtarld\ApiPlatformMsBundle\Validator;

use LogicException;
use Mtarld\ApiPlatformMsBundle\Resource\ExistenceChecker;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Throwable;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class ResourceExistValidator extends ConstraintValidator
{
    private $existenceChecker;
    private $logger;

    public function __construct(
        ExistenceChecker $existenceChecker,
        LoggerInterface $logger
    ) {
        $this->existenceChecker = $existenceChecker;
        $this->logger = $logger;
    }

    /**
     * @param ResourceExist $constraint
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function validate($value, Constraint $constraint): void
    {
        if (empty($constraint->microservice)) {
            throw new LogicException(sprintf("You must specify 'microservice' attribute of '%s'", ResourceExist::class));
        }

        if (null === $value) {
            return;
        }

        $this->validateIris(is_array($value) ? $value : [$value], $constraint);
    }

    private function validateIris(array $iris, ResourceExist $constraint): void
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

    private function handleExistenceCheckerHttpException(Throwable $exception, ResourceExist $constraint): void
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
