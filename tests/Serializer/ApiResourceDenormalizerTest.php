<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\Serializer;

use Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\Dto\ColorResourceDto;
use Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\Dto\HairResourceDto;
use Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\Dto\PuppyResourceDto;
use Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\Entity\Color;
use Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\Entity\Hair;
use Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\Entity\Puppy;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @group denormalizer
 * @group resource
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class ApiResourceDenormalizerTest extends KernelTestCase
{
    public function setUp(): void
    {
        static::bootKernel();
    }

    /**
     * @dataProvider formatsDataProvider
     *
     * @testdox Can denormalize resource with $format format
     */
    public function testApiResourceDenormalization(string $format): void
    {
        $entity = new Puppy(1, 'foo');

        /** @var SerializerInterface $serializer */
        $serializer = static::getContainer()->get(SerializerInterface::class);
        $serializedEntity = $serializer->serialize($entity, $format);

        /** @var Puppy $deserializedEntity */
        $deserializedEntity = $serializer->deserialize($serializedEntity, PuppyResourceDto::class, $format);
        self::assertEquals(new PuppyResourceDto('/puppies/1', 'foo'), $deserializedEntity);
    }

    /**
     * @dataProvider formatsDataProvider
     *
     * @testdox Can denormalize nested resource with $format format
     */
    public function testNestedApiResourceDenormalization(string $format): void
    {
        $nestedEntity = new Color(1, '#000000');
        $nestedEntityCollection = [
            new Hair(1, 10, new Color(1, '#000000')),
        ];
        $entity = new Puppy(1, 'foo', $nestedEntity, $nestedEntityCollection);

        /** @var SerializerInterface $serializer */
        $serializer = static::getContainer()->get(SerializerInterface::class);
        $serializedEntity = $serializer->serialize($entity, $format, ['api_included' => ['color', 'hairs']]);

        /** @var Puppy $deserializedEntity */
        $deserializedEntity = $serializer->deserialize($serializedEntity, PuppyResourceDto::class, $format);
        self::assertEquals(
            new PuppyResourceDto(
                '/puppies/1',
                'foo',
                new ColorResourceDto('/colors/1', '#000000'),
                [
                    new HairResourceDto('/hairs/1', 10, new ColorResourceDto('/colors/1', '#000000')),
                ]
            ),
            $deserializedEntity
        );
    }

    public function formatsDataProvider(): iterable
    {
        yield ['jsonld'];
        yield ['jsonapi'];
        yield ['jsonhal'];
    }
}
