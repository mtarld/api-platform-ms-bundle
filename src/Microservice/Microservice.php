<?php

namespace Mtarld\ApiPlatformMsBundle\Microservice;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @final
 */
class Microservice
{
    /**
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @Assert\Url
     */
    private $baseUri;

    /**
     * @Assert\NotBlank
     */
    private $format;

    public function __construct(string $name, string $baseUri, string $format)
    {
        $this->name = $name;
        $this->baseUri = $baseUri;
        $this->format = $format;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getBaseUri(): string
    {
        return $this->baseUri;
    }

    public function getFormat(): string
    {
        return $this->format;
    }
}
