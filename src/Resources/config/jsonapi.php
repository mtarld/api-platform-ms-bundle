<?php

namespace Mtarld\ApiPlatformMsBundle\DependencyInjection\Loader\Configurator;

use ApiPlatform\Serializer\JsonEncoder;
use Mtarld\ApiPlatformMsBundle\Serializer\JsonApi\ApiResourceDenormalizer;
use Mtarld\ApiPlatformMsBundle\Serializer\JsonApi\CollectionDenormalizer;
use Mtarld\ApiPlatformMsBundle\Serializer\JsonApi\ConstraintViolationListDenormalizer;
use Mtarld\ApiPlatformMsBundle\Serializer\JsonApi\ObjectDenormalizer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->defaults()
            ->private()
        ->set('api_platform_ms.jsonapi.encoder', JsonEncoder::class)
            ->args([
                'jsonhal',
            ])
            ->tag('serializer.encoder')
        ->set('api_platform_ms.jsonapi.denormalizer.constraint_violation_list', ConstraintViolationListDenormalizer::class)
            ->call('setNameConverter', [
                service('api_platform.name_converter')->ignoreOnInvalid(),
            ])
            ->tag('serializer.normalizer', [
                'priority' => -880, // Run before api_platform.jsonapi.normalizer.item
            ])
        ->set('api_platform_ms.jsonapi.denormalizer.collection', CollectionDenormalizer::class)
            ->tag('serializer.normalizer', [
                'priority' => -870, // Run before api_platform_ms.jsonapi.denormalizer.object
            ])
        ->set('api_platform_ms.jsonapi.denormalizer.api_resource', ApiResourceDenormalizer::class)
            ->tag('serializer.normalizer', [
                'priority' => -870, // Run before api_platform_ms.jsonapi.denormalizer.object
            ])
        ->set('api_platform_ms.jsonapi.denormalizer.object', ObjectDenormalizer::class)
            ->tag('serializer.normalizer', [
                'priority' => -880, // Run before api_platform.jsonapi.normalizer.item
            ])
    ;
};
