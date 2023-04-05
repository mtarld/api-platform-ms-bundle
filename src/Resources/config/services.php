<?php

namespace Mtarld\ApiPlatformMsBundle\DependencyInjection\Loader\Configurator;

use Mtarld\ApiPlatformMsBundle\ApiResource\ExistenceChecker;
use Mtarld\ApiPlatformMsBundle\Collection\PaginatedCollectionIterator;
use Mtarld\ApiPlatformMsBundle\Controller\ApiResourceExistenceCheckerAction;
use Mtarld\ApiPlatformMsBundle\EventListener\RequestLoggerListener;
use Mtarld\ApiPlatformMsBundle\HttpClient\GenericHttpClient;
use Mtarld\ApiPlatformMsBundle\Microservice\MicroservicePool;
use Mtarld\ApiPlatformMsBundle\Routing\RouteLoader;
use Mtarld\ApiPlatformMsBundle\Validator\ApiResourceExistValidator;
use Mtarld\ApiPlatformMsBundle\Validator\FormatEnabledValidator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->defaults()
            ->private()
        ->set('api_platform_ms.route_loader', RouteLoader::class)
            ->args([
                param('api_platform_ms.name'),
                param('api_platform_ms.hosts'),
            ])
            ->tag('routing.route_loader')
        ->set('api_platform_ms.microservice_pool', MicroservicePool::class)
            ->args([
                service('validator'),
                param('api_platform_ms.microservices'),
            ])
        ->alias(MicroservicePool::class, 'api_platform_ms.microservice_pool')
        ->set(ApiResourceExistenceCheckerAction::class, ApiResourceExistenceCheckerAction::class)
            ->args([
                service('serializer'),
                service('api_platform.iri_converter'),
                service('api_platform.validator'),
            ])
            ->tag('controller.service_arguments')
        ->set('api_platform_ms.http_client.generic', GenericHttpClient::class)
            ->args([
                service('serializer'),
                service('api_platform_ms.http_client'),
                tagged_iterator('api_platform_ms.authentication_header_provider'),
                service('event_dispatcher'),
            ])
        ->alias(GenericHttpClient::class, 'api_platform_ms.http_client.generic')
        ->set('api_platform_ms.api_resource.existence_checker', ExistenceChecker::class)
            ->args([
                service('api_platform_ms.http_client.generic'),
                service('serializer'),
                service('api_platform_ms.microservice_pool'),
            ])
        ->set('api_platform_ms.validator.api_resource_exist', ApiResourceExistValidator::class)
            ->args([
                service('api_platform_ms.api_resource.existence_checker'),
            ])
            ->call('setLogger', [
                service('logger'),
            ])
            ->tag('validator.constraint_validator')
        ->alias(ApiResourceExistValidator::class, 'api_platform_ms.validator.api_resource_exist')
        ->set('api_platform_ms.validator.format_enabled', FormatEnabledValidator::class)
            ->args([
                param('api_platform_ms.enabled_formats'),
            ])
            ->tag('validator.constraint_validator')
        ->set('api_platform_ms.collection.paginated_collection_iterator', PaginatedCollectionIterator::class)
            ->args([
                service('api_platform_ms.http_client.generic'),
                service('serializer'),
            ])
        ->alias(PaginatedCollectionIterator::class, 'api_platform_ms.collection.paginated_collection_iterator')
        ->set('api_platform_ms.request_logger_listener', RequestLoggerListener::class)
            ->args([
                service('logger'),
            ])
            ->tag('kernel.event_listener')
    ;
};
