<?php

namespace Mtarld\ApiPlatformMsBundle;

use Mtarld\ApiPlatformMsBundle\DependencyInjection\ApiPlatformMsExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ApiPlatformMsBundle extends Bundle
{
    public function getContainerExtension(): ExtensionInterface
    {
        return new ApiPlatformMsExtension();
    }
}
