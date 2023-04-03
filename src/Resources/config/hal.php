<?php

namespace Mtarld\ApiPlatformMsBundle\DependencyInjection\Loader\Configurator;

use ApiPlatform\Serializer\JsonEncoder;
use Mtarld\ApiPlatformMsBundle\Serializer\Hal\ApiResourceDenormalizer;
use Mtarld\ApiPlatformMsBundle\Serializer\Hal\CollectionDenormalizer;
use Mtarld\ApiPlatformMsBundle\Serializer\Hal\ConstraintViolationListDenormalizer;
use Mtarld\ApiPlatformMsBundle\Serializer\Hal\ObjectDenormalizer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->defaults()
            ->private()
        ->set('api_platform_ms.hal.encoder', JsonEncoder::class)
            ->args([
                'jsonhal',
            ])
            ->tag('serializer.encoder')
        ->set('api_platform_ms.hal.denormalizer.constraint_violation_list', ConstraintViolationListDenormalizer::class)
            ->call('setNameConverter', [
                service('api_platform.name_converter')->ignoreOnInvalid(),
            ])
            ->tag('serializer.normalizer', [
                'priority' => -880, // Run before api_platform.hal.normalizer.item
            ])
        ->set('api_platform_ms.hal.denormalizer.collection', CollectionDenormalizer::class)
            ->tag('serializer.normalizer', [
                'priority' => -870, // Run before api_platform_ms.hal.denormalizer.object
            ])
        ->set('api_platform_ms.hal.denormalizer.api_resource', ApiResourceDenormalizer::class)
            ->tag('serializer.normalizer', [
                'priority' => -870, // Run before api_platform_ms.hal.denormalizer.object
            ])
        ->set('api_platform_ms.hal.denormalizer.object', ObjectDenormalizer::class)
            ->tag('serializer.normalizer', [
                'priority' => -880, // Run before api_platform.hal.normalizer.item
            ])
    ;
};
