<?php

namespace Mtarld\ApiPlatformMsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @final @internal
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @psalm-suppress PossiblyUndefinedMethod
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder = new TreeBuilder('api_platform_ms');
        $builder->getRootNode()
            ->children()
                ->scalarNode('http_client')->defaultValue(HttpClientInterface::class)->treatNullLike(HttpClientInterface::class)->end()
                ->scalarNode('name')->isRequired()->end()
                ->arrayNode('hosts')->variablePrototype()->end()->end()
                ->arrayNode('microservices')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('base_uri')->isRequired()->end()
                            ->scalarNode('api_path')->end()
                            ->scalarNode('format')->isRequired()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $builder;
    }
}
