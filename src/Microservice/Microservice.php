<?php

namespace Mtarld\ApiPlatformMsBundle\Microservice;

use Mtarld\ApiPlatformMsBundle\Validator\FormatEnabled;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @final
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class Microservice
{
    public function __construct(
        #[Assert\NotBlank] private readonly string $name,
        #[Assert\Url] private readonly string $baseUri,
        private readonly string $apiPath,
        #[FormatEnabled] #[Assert\NotBlank] private readonly string $format
    ) {
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
