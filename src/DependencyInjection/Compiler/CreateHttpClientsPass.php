<?php

namespace Mtarld\ApiPlatformMsBundle\DependencyInjection\Compiler;

use Mtarld\ApiPlatformMsBundle\HttpClient\MicroserviceHttpClient;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

// Help opcache.preload discover always-needed symbols
class_exists(MicroserviceHttpClient::class);

/**
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class CreateHttpClientsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        /** @var array<string, array> $microservices */
        $microservices = $container->getParameter('api_platform_ms.microservices');

        foreach (array_keys($microservices) as $microserviceName) {
            $container->setDefinition(
                sprintf('api_platform_ms.http_client.microservice.%s', $microserviceName),
                (new Definition(MicroserviceHttpClient::class, [
                    $container->getDefinition('api_platform_ms.http_client.generic'),
                    $container->getDefinition('api_platform_ms.microservice_pool'),
                    $microserviceName,
                ]))->setPublic(true)
            );
        }
    }
}
