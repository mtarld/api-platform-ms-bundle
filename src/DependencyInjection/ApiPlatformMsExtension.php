<?php

namespace Mtarld\ApiPlatformMsBundle\DependencyInjection;

use Mtarld\ApiPlatformMsBundle\Microservice\MicroservicePool;
use Mtarld\ApiPlatformMsBundle\Routing\RouteLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * @internal
 */
class ApiPlatformMsExtension extends Extension
{
    public function getAlias(): string
    {
        return 'api_platform_ms';
    }

    /**
     * @param array<array-key, mixed> $configs
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        if (null === $configuration = $this->getConfiguration($configs, $container)) {
            return;
        }

        $config = $this->processConfiguration($configuration, $configs);

        $container->setAlias('api_platform_ms.http_client', $config['http_client']);

        $container->getDefinition(MicroservicePool::class)->setArgument('$microserviceConfigs', $config['microservices']);
        $container->getDefinition(RouteLoader::class)->setArgument('$name', $config['name'])->setArgument('$hosts', $config['hosts']);
    }
}
