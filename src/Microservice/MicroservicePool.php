<?php

namespace Mtarld\ApiPlatformMsBundle\Microservice;

use Mtarld\ApiPlatformMsBundle\Exception\MicroserviceNotConfiguredException;

class MicroservicePool
{
    /**
     * @var array<array-key, array<array-key, mixed>>
     */
    private $configs;

    /**
     * @var array<array-key, Microservice>
     */
    private $microservices = [];

    public function __construct(array $microserviceConfigs = [])
    {
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

    private function createMicroservice(string $name): Microservice
    {
        if (!$this->has($name)) {
            throw new MicroserviceNotConfiguredException($name);
        }

        return new Microservice($this->configs[$name]['base_uri']);
    }
}
