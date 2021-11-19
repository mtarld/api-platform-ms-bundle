<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\Microservice;

use Mtarld\ApiPlatformMsBundle\Exception\MicroserviceConfigurationException;
use Mtarld\ApiPlatformMsBundle\Exception\MicroserviceNotConfiguredException;
use Mtarld\ApiPlatformMsBundle\Microservice\Microservice;
use Mtarld\ApiPlatformMsBundle\Microservice\MicroservicePool;
use Mtarld\ApiPlatformMsBundle\Tests\BcLayer\BcLayerKernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @group microservice
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class MicroservicePoolTest extends BcLayerKernelTestCase
{
    public function setUp(): void
    {
        static::bootKernel();
    }

    public function testHasMicroservice(): void
    {
        /** @var MicroservicePool $pool */
        $pool = static::getContainer()->get(MicroservicePool::class);

        self::assertFalse($pool->has('foo'));
        self::assertTrue($pool->has('bar'));
    }

    public function testGetMicroservice(): void
    {
        /** @var MicroservicePool $pool */
        $pool = static::getContainer()->get(MicroservicePool::class);

        self::assertEquals(new Microservice('bar', 'https://localhost', '/api', 'jsonld'), $pool->get('bar'));

        $this->expectException(MicroserviceNotConfiguredException::class);
        $pool->get('foo');
    }

    public function testMicroserviceConfigurationUriValidation(): void
    {
        /** @var MicroservicePool $pool */
        $pool = static::getContainer()->get(MicroservicePool::class);

        $this->expectException(MicroserviceConfigurationException::class);
        $pool->get('wrong_uri');
    }

    public function testMicroserviceConfigurationFormatValidation(): void
    {
        $pool = new MicroservicePool(static::getContainer()->get(ValidatorInterface::class), [
            'microservice' => [
                'base_uri' => 'https://localhost',
                'api_path' => '/api',
                'format' => 'wrong_format',
            ],
        ]);

        $this->expectException(MicroserviceConfigurationException::class);
        $this->expectExceptionMessage("'microservice' microservice is wrongly configured. 'format': 'wrong_format' format is not enabled.");
        $pool->get('microservice');
    }
}
