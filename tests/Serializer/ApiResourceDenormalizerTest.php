<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\Serializer;

use Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\Dto\PuppyResourceDto;
use Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\Entity\Puppy;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @group denormalizer
 * @group resource
 */
class ApiResourceDenormalizerTest extends KernelTestCase
{
    public function setUp(): void
    {
        static::bootKernel();
    }

    /**
     * @dataProvider formatsDataProvider
     * @testdox Can denormalize resource with $format format
     */
    public function testApiResourceDenormalization(string $format): void
    {
        $entity = new Puppy(1, 'foo');

        /** @var SerializerInterface $serializer */
        $serializer = static::$container->get(SerializerInterface::class);
        $serializedEntity = $serializer->serialize($entity, $format);

        /** @var Puppy $deserializedEntity */
        $deserializedEntity = $serializer->deserialize($serializedEntity, PuppyResourceDto::class, $format);
        $this->assertEquals(new PuppyResourceDto('/puppies/1', 1, 'foo'), $deserializedEntity);
    }

    public function formatsDataProvider(): iterable
    {
        yield ['jsonld'];
        yield ['jsonapi'];
        yield ['jsonhal'];
    }
}
