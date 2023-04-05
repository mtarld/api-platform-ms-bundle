<?php

namespace Mtarld\ApiPlatformMsBundle\DependencyInjection;

use Mtarld\ApiPlatformMsBundle\HttpClient\AuthenticationHeaderProviderInterface;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

// Help opcache.preload discover always-needed symbols
class_exists(AuthenticationHeaderProviderInterface::class);
class_exists(FileLocator::class);
class_exists(PhpFileLoader::class);

/**
 * @final @internal
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class ApiPlatformMsExtension extends Extension
{
    final public const FORMAT_CONFIGURATION_FILE_MAPPING = [
        'jsonld' => 'hydra.php',
        'jsonapi' => 'jsonapi.php',
        'jsonhal' => 'hal.php',
    ];

    public function getAlias(): string
    {
        return 'api_platform_ms';
    }

    /**
     * @param array<array-key, mixed> $configs
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.php');

        if (null === $configuration = $this->getConfiguration($configs, $container)) {
            return;
        }

        $config = $this->processConfiguration($configuration, $configs);

        $formats = array_values(
            array_unique(
                array_map(
                    static fn (array $microservice): string => $microservice['format'],
                    array_filter($config['microservices'],
                        static fn (array $microservice): bool => isset(self::FORMAT_CONFIGURATION_FILE_MAPPING[$microservice['format']])
                    )
                )
            )
        );

        foreach ($formats as $format) {
            $loader->load(self::FORMAT_CONFIGURATION_FILE_MAPPING[$format]);
        }

        $container->setAlias('api_platform_ms.http_client', $config['http_client']);

        $container->setParameter('api_platform_ms.name', $config['name']);
        $container->setParameter('api_platform_ms.hosts', $config['hosts']);
        $container->setParameter('api_platform_ms.microservices', $config['microservices']);
        $container->setParameter('api_platform_ms.enabled_formats', $formats);

        $container->registerForAutoconfiguration(AuthenticationHeaderProviderInterface::class)->addTag('api_platform_ms.authentication_header_provider');

        if (!$config['log_request']) {
            $container->removeDefinition('api_platform_ms.request_logger_listener');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(array $config, ContainerBuilder $container): ?ConfigurationInterface
    {
        /** @var bool $debug */
        $debug = $container->getParameter('kernel.debug');

        return new Configuration($debug);
    }
}
