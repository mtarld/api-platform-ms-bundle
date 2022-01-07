<?php

namespace Mtarld\ApiPlatformMsBundle\Microservice;

use IteratorAggregate;
use Mtarld\ApiPlatformMsBundle\Exception\MicroserviceConfigurationException;
use Mtarld\ApiPlatformMsBundle\Exception\MicroserviceNotConfiguredException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Traversable;

// Help opcache.preload discover always-needed symbols
class_exists(Microservice::class);
class_exists(MicroserviceConfigurationException::class);
class_exists(MicroserviceNotConfiguredException::class);

/**
 * @final
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class MicroservicePool implements IteratorAggregate
{
    /**
     * @var array<string, array<string, string>>
     */
    private array $configs;

    /**
     * @var array<string, Microservice>
     */
    private array $microservices = [];

    public function __construct(
        private ValidatorInterface $validator,
        array                      $microserviceConfigs = []
    ) {
        $this->configs = $microserviceConfigs;
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->configs);
    }

    public function get(string $name): Microservice
    {
        if (!array_key_exists($name, $this->microservices)) {
            $this->microservices[$name] = $this->createMicroservice($name);
        }

        return $this->microservices[$name];
    }

    public function getIterator(): Traversable
    {
        foreach (array_keys($this->configs) as $name) {
            yield $this->get($name);
        }
    }

    private function createMicroservice(string $name): Microservice
    {
        if (!$this->has($name)) {
            throw new MicroserviceNotConfiguredException($name);
        }

        $config = $this->configs[$name];

        $microservice = new Microservice($name, $config['base_uri'], $config['api_path'] ?? '', $config['format']);
        $this->validateMicroservice($microservice);

        return $microservice;
    }

    /**
     * @throws MicroserviceConfigurationException
     */
    private function validateMicroservice(Microservice $microservice): void
    {
        $violations = $this->validator->validate($microservice);

        if ($violations->has(0)) {
            throw new MicroserviceConfigurationException($microservice->getName(), sprintf("'%s': %s", $violations->get(0)->getPropertyPath(), (string) $violations->get(0)->getMessage()));
        }
    }
}
