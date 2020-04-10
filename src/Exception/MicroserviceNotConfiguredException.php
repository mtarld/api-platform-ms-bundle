<?php

namespace Mtarld\ApiPlatformMsBundle\Exception;

use LogicException;

class MicroserviceNotConfiguredException extends LogicException
{
    public function __construct(string $name)
    {
        parent::__construct(
            sprintf("'%s' microservice configuration wasn't found. Please configure it under api_platform_ms.microservices configuration key", $name),
            0,
            null
        );
    }
}
