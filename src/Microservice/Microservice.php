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
    private string $name;

    /**
     * @Assert\Url
     */
    private string $baseUri;

    /**
     * @Assert\NotBlank
     * @Mtarld\ApiPlatformMsBundle\Validator\FormatEnabled
     */
    private string $format;

    public function __construct(string $name, string $baseUri, private string $apiPath, string $format)
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

    public function getApiPath(): string
    {
        return $this->apiPath;
    }

    public function getFormat(): string
    {
        return $this->format;
    }
}
