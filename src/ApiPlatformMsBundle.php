<?php

namespace Mtarld\ApiPlatformMsBundle;

use Mtarld\ApiPlatformMsBundle\DependencyInjection\Compiler\CreateHttpClientsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @final
 */
class ApiPlatformMsBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new CreateHttpClientsPass());
    }
}
