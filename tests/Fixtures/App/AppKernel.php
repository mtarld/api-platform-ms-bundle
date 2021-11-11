<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App;

use ApiPlatform\Core\Bridge\Symfony\Bundle\ApiPlatformBundle;
use Mtarld\ApiPlatformMsBundle\ApiPlatformMsBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

/**
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
if (Kernel::VERSION_ID <= 50100) {
    class AppKernel extends Kernel
    {
        use MicroKernelTrait;

        public function registerBundles(): array
        {
            return [
                new FrameworkBundle(),
                new TwigBundle(),
                new ApiPlatformBundle(),
                new ApiPlatformMsBundle(),
            ];
        }

        protected function configureRoutes(RouteCollectionBuilder $routes): void
        {
            $routes->import(__DIR__.'/config/routing.yml');
        }

        protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
        {
            $container->setParameter('kernel.project_dir', __DIR__);

            $loader->load(__DIR__.'/config/config.yml');
        }
    }
} else {
    # Symfony > 5.1
    class AppKernel extends Kernel
    {
        use MicroKernelTrait;

        public function registerBundles(): array
        {
            return [
                new FrameworkBundle(),
                new TwigBundle(),
                new ApiPlatformBundle(),
                new ApiPlatformMsBundle(),
            ];
        }

        protected function configureRoutes(RoutingConfigurator $routes): void
        {
            $routes->import(__DIR__.'/config/routing.yml');
        }

        protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
        {
            $container->setParameter('kernel.project_dir', __DIR__);

            $loader->load(__DIR__.'/config/config.yml');
        }
    }
}
