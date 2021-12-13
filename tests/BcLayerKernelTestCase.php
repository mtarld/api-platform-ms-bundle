<?php

namespace Mtarld\ApiPlatformMsBundle\Tests;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;

// Remove after support of symfony support of 4.4 is dropped
abstract class BcLayerKernelTestCase extends KernelTestCase
{
    private static $kernelContainer;

    protected static function bootKernel(array $options = []): KernelInterface
    {
        $kernel = parent::bootKernel($options);

        self::$kernelContainer = $kernel->getContainer();

        return $kernel;
    }

    protected static function getContainer(): ContainerInterface
    {
        if (method_exists(parent::class, 'getContainer')) {
            return parent::getContainer();
        }

        if (!static::$booted) {
            static::bootKernel();
        }

        try {
            return self::$kernelContainer->get('test.service_container');
        } catch (ServiceNotFoundException $e) {
            throw new LogicException('Could not find service "test.service_container". Try updating the "framework.test" config to "true".', 0, $e);
        }
    }
}
