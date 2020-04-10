<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\Microservice;

use Mtarld\ApiPlatformMsBundle\Exception\MicroserviceNotConfiguredException;
use Mtarld\ApiPlatformMsBundle\Microservice\Microservice;
use Mtarld\ApiPlatformMsBundle\Microservice\MicroservicePool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @group functional
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

        $this->assertEquals(new Microservice('https://localhost'), $pool->get('bar'));

        $this->expectException(MicroserviceNotConfiguredException::class);
        $pool->get('foo');
    }
}
