<?php

namespace Mtarld\ApiPlatformMsBundle\Event;

use Mtarld\ApiPlatformMsBundle\Microservice\Microservice;

/**
 * @final
 *
 * @psalm-immutable
 *
 * @author Smaïne Milianni <smaine.milianni@gmail.com>
 */
class RequestEvent
{
    public function __construct(private readonly Microservice $microservice, private readonly string $method, private readonly string $uri, private readonly array $options)
    {
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
