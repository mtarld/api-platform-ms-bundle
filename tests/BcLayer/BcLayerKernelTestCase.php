<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\BcLayer;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;

// Remove after support of symfony support of 4.4 is dropped
if (Kernel::VERSION_ID <= 50000) {
    class BcLayerKernelTestCase extends KernelTestCase
    {
        protected static $kernelContainer;

        /**
         * Boots the Kernel for this test.
         *
         * @return KernelInterface
         */
        protected static function bootKernel(array $options = [])
        {
            static::ensureKernelShutdown();

            static::$kernel = static::createKernel($options);
            static::$kernel->boot();
            static::$booted = true;

            self::$kernelContainer = $container = static::$kernel->getContainer();
            static::$container = $container->has('test.service_container') ? $container->get('test.service_container') : $container;

            return static::$kernel;
        }

        /**
         * Provides a dedicated test container with access to both public and private
         * services. The container will not include private services that have been
         * inlined or removed. Private services will be removed when they are not
         * used by other services.
         *
         * Using this method is the best way to get a container from your test code.
         */
        protected static function getContainer(): ContainerInterface
        {
            if (!static::$booted) {
                static::bootKernel();
            }

            try {
                return self::$kernelContainer->get('test.service_container');
            } catch (ServiceNotFoundException $e) {
                throw new \LogicException('Could not find service "test.service_container". Try updating the "framework.test" config to "true".', 0, $e);
            }
        }
    }
} else {
    class BcLayerKernelTestCase extends KernelTestCase
    {
    }
}
