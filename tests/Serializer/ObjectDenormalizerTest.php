<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\Serializer;

use Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\Dto\PuppyDto;
use Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\Entity\Puppy;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @group denormalizer
 * @group object
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class ObjectDenormalizerTest extends KernelTestCase
{
    public function setUp(): void
    {
        static::bootKernel();
    }

    /**
     * @dataProvider formatsDataProvider
     * @testdox Can denormalize object with $format format
     */
    public function testObjectDenormalization(string $format): void
    {
        $entity = new Puppy(1, 'foo');

        /** @var SerializerInterface $serializer */
        $serializer = static::getContainer()->get(SerializerInterface::class);
        $serializedEntity = $serializer->serialize($entity, $format);

        /** @var Puppy $deserializedEntity */
        $deserializedEntity = $serializer->deserialize($serializedEntity, PuppyDto::class, $format);
        self::assertEquals(new PuppyDto(1, 'foo'), $deserializedEntity);
    }

    public function formatsDataProvider(): iterable
    {
        yield ['jsonld'];
        yield ['jsonapi'];
        yield ['jsonhal'];
    }
}
