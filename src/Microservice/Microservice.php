<?php

namespace Mtarld\ApiPlatformMsBundle\Microservice;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @final
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
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

    private $apiPath;

    /**
     * @Assert\NotBlank
     * @Mtarld\ApiPlatformMsBundle\Validator\FormatEnabled
     */
    private $format;

    public function __construct(string $name, string $baseUri, string $apiPath, string $format)
    {
        $this->name = $name;
        $this->baseUri = $baseUri;
        $this->apiPath = $apiPath;
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

    public function getApiPath(): string
    {
        return $this->apiPath;
    }

    public function getFormat(): string
    {
        return $this->format;
    }
}
