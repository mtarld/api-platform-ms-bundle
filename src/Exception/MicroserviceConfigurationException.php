<?php

namespace Mtarld\ApiPlatformMsBundle\Exception;

/**
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class MicroserviceConfigurationException extends \LogicException
{
    public function __construct(string $name, string $message)
    {
        parent::__construct(sprintf("'%s' microservice is wrongly configured. %s", $name, $message), 0, null);
    }
}
