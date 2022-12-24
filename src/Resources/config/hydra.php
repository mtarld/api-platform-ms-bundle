<?php

namespace Mtarld\ApiPlatformMsBundle\DependencyInjection\Loader\Configurator;

use ApiPlatform\Serializer\JsonEncoder;
use Mtarld\ApiPlatformMsBundle\Serializer\Hydra\ApiResourceDenormalizer;
use Mtarld\ApiPlatformMsBundle\Serializer\Hydra\CollectionDenormalizer;
use Mtarld\ApiPlatformMsBundle\Serializer\Hydra\ConstraintViolationListDenormalizer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->defaults()
            ->private()
        ->set('api_platform_ms.hydra.encoder', JsonEncoder::class)
            ->args([
                'jsonld',
            ])
            ->tag('serializer.encoder')
        ->set('api_platform_ms.hydra.denormalizer.constraint_violation_list', ConstraintViolationListDenormalizer::class)
            ->call('setNameConverter', [
                service('api_platform.name_converter')->ignoreOnInvalid(),
            ])
            ->tag('serializer.normalizer', [
                'priority' => -985, // Run before api_platform.jsonld.normalizer.object
            ])
        ->set('api_platform_ms.hydra.denormalizer.collection', CollectionDenormalizer::class)
            ->tag('serializer.normalizer', [
                'priority' => -985, // Run before api_platform.jsonld.normalizer.object
            ])
        ->set('api_platform_ms.hydra.denormalizer.api_resource', ApiResourceDenormalizer::class)
            ->tag('serializer.normalizer', [
                'priority' => -985, // Run before api_platform.jsonld.normalizer.object
            ])
    ;
};
