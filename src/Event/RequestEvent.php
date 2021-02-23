<?php

namespace Mtarld\ApiPlatformMsBundle\Event;

use Mtarld\ApiPlatformMsBundle\Microservice\Microservice;

/**
 * @final
 * @psalm-immutable
 *
 * @author Smaïne Milianni <smaine.milianni@gmail.com>
 */
class RequestEvent
{
    private $microservice;
    private $method;
    private $uri;
    private $options;

    public function __construct(Microservice $microservice, string $method, string $uri, array $options)
    {
        $this->microservice = $microservice;
        $this->method = $method;
        $this->uri = $uri;
        $this->options = $options;
    }

    public function getMicroservice(): Microservice
    {
        return $this->microservice;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
