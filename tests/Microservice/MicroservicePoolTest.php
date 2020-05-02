<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\Microservice;

use Mtarld\ApiPlatformMsBundle\Exception\MicroserviceConfigurationException;
use Mtarld\ApiPlatformMsBundle\Exception\MicroserviceNotConfiguredException;
use Mtarld\ApiPlatformMsBundle\Microservice\Microservice;
use Mtarld\ApiPlatformMsBundle\Microservice\MicroservicePool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @group microservice
 */
class MicroservicePoolTest extends KernelTestCase
{
    public function setUp(): void
    {
        static::bootKernel();
    }

    public function testHasMicroservice(): void
    {
        /** @var MicroservicePool $pool */
        $pool = static::$container->get(MicroservicePool::class);

        $this->assertFalse($pool->has('foo'));
        $this->assertTrue($pool->has('bar'));
    }

    public function testGetMicroservice(): void
    {
        /** @var MicroservicePool $pool */
        $pool = static::$container->get(MicroservicePool::class);

        $this->assertEquals(new Microservice('bar', 'https://localhost', 'jsonld'), $pool->get('bar'));

        $this->expectException(MicroserviceNotConfiguredException::class);
        $pool->get('foo');
    }

    public function testMicroserviceConfigurationUriValidation(): void
    {
        /** @var MicroservicePool $pool */
        $pool = static::$container->get(MicroservicePool::class);

        $this->expectException(MicroserviceConfigurationException::class);
        $pool->get('wrong_uri');
    }

    public function testMicroserviceConfigurationFormatValidation(): void
    {
        /** @var MicroservicePool $pool */
        $pool = static::$container->get(MicroservicePool::class);

        $this->expectException(MicroserviceConfigurationException::class);
        $pool->get('wrong_format');
    }
}
