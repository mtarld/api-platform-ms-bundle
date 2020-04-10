<?php

namespace Mtarld\ApiPlatformMsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder = new TreeBuilder('api_platform_ms');
        $builder->getRootNode()
            ->children()
            ->end()
        ;

        return $builder;
    }
}
