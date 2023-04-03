<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\Serializer;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\State\Pagination\ArrayPaginator;
use Mtarld\ApiPlatformMsBundle\Collection\Collection;
use Mtarld\ApiPlatformMsBundle\Collection\Pagination;
use Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\Dto\PuppyDto;
use Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\Dto\PuppyResourceDto;
use Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\Entity\Puppy;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @group denormalizer
 * @group collection
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class CollectionDenormalizerTest extends KernelTestCase
{
    public function setUp(): void
    {
        static::bootKernel();
    }

    /**
     * @dataProvider formatsDataProvider
     *
     * @testdox Can denormalize resource collection with $format format
     */
    public function testResourceCollectionDenormalization(string $format): void
    {
        $entityCollection = [new Puppy(1, 'foo'), new Puppy(2, 'bar'), new Puppy(3, 'baz')];
        $dtoCollection = [new PuppyResourceDto('/puppies/1', 'foo'), new PuppyResourceDto('/puppies/2', 'bar'), new PuppyResourceDto('/puppies/3', 'baz')];

        /** @var SerializerInterface $serializer */
        $serializer = static::getContainer()->get(SerializerInterface::class);
        $serializedCollection = $serializer->serialize($entityCollection, $format, ['_api_operation' => GetCollection::class, '_api_resource_class' => Puppy::class, '_api_operation_name' => 'foo']);

        /** @var Collection $deserializedCollection */
        $deserializedCollection = $serializer->deserialize($serializedCollection, Collection::class.'<'.PuppyResourceDto::class.'>', $format);
        self::assertEquals(new Collection($dtoCollection, 3), $deserializedCollection);

        self::assertFalse($deserializedCollection->hasPagination());

        foreach ($deserializedCollection as $i => $item) {
            self::assertEquals($dtoCollection[$i], $item);
        }
    }

    /**
     * @dataProvider formatsDataProvider
     *
     * @testdox Can denormalize paginated resource collection with $format format
     */
    public function testPaginatedResourceCollectionDenormalization(string $format): void
    {
        $entityCollection = new ArrayPaginator([new Puppy(1, 'foo'), new Puppy(2, 'bar'), new Puppy(3, 'baz')], 0, 2);
        $dtoCollection = [new PuppyResourceDto('/puppies/1', 'foo'), new PuppyResourceDto('/puppies/2', 'bar')];

        /** @var SerializerInterface $serializer */
        $serializer = static::getContainer()->get(SerializerInterface::class);
        $serializedCollection = $serializer->serialize($entityCollection, $format, ['_api_operation' => GetCollection::class, '_api_resource_class' => Puppy::class, '_api_operation_name' => 'foo']);

        /** @var Collection $deserializedCollection */
        $deserializedCollection = $serializer->deserialize($serializedCollection, Collection::class.'<'.PuppyResourceDto::class.'>', $format);
        self::assertEquals(new Collection($dtoCollection, 3, new Pagination('/?page=1', '/?page=1', '/?page=2', null, '/?page=2')), $deserializedCollection);

        self::assertTrue($deserializedCollection->hasPagination());
        self::assertEquals('/?page=1', $deserializedCollection->getPagination()->getCurrent());
        self::assertNull($deserializedCollection->getPagination()->getPrevious());
        self::assertEquals('/?page=2', $deserializedCollection->getPagination()->getNext());
        self::assertEquals('/?page=1', $deserializedCollection->getPagination()->getFirst());
        self::assertEquals('/?page=2', $deserializedCollection->getPagination()->getLast());

        foreach ($deserializedCollection as $i => $deserializedElement) {
            self::assertEquals($dtoCollection[$i], $deserializedElement);
        }

        $entityCollection = new ArrayPaginator([new Puppy(1, 'foo'), new Puppy(2, 'bar'), new Puppy(3, 'baz')], 2, 2);
        $dtoCollection = [new PuppyResourceDto('/puppies/3', 'baz')];

        /** @var SerializerInterface $serializer */
        $serializer = static::getContainer()->get(SerializerInterface::class);
        $serializedCollection = $serializer->serialize($entityCollection, $format, ['_api_operation' => GetCollection::class, '_api_resource_class' => Puppy::class, '_api_operation_name' => 'foo']);

        /** @var Collection $deserializedCollection */
        $deserializedCollection = $serializer->deserialize($serializedCollection, Collection::class.'<'.PuppyResourceDto::class.'>', $format);
        self::assertEquals(new Collection($dtoCollection, 3, new Pagination('/?page=2', '/?page=1', '/?page=2', '/?page=1', null)), $deserializedCollection);

        self::assertTrue($deserializedCollection->hasPagination());
        self::assertEquals('/?page=2', $deserializedCollection->getPagination()->getCurrent());
        self::assertEquals('/?page=1', $deserializedCollection->getPagination()->getPrevious());
        self::assertNull($deserializedCollection->getPagination()->getNext());
        self::assertEquals('/?page=1', $deserializedCollection->getPagination()->getFirst());
        self::assertEquals('/?page=2', $deserializedCollection->getPagination()->getLast());

        self::assertEquals($dtoCollection[0], $deserializedCollection->getIterator()->current());
    }

    /**
     * @dataProvider formatsDataProvider
     *
     * @testdox Can denormalize raw collection with $format format
     */
    public function testRawCollectionDenormalization(string $format): void
    {
        $dtoCollection = [new PuppyDto(1, 'foo'), new PuppyDto(2, 'bar'), new PuppyDto(3, 'baz')];

        /** @var SerializerInterface $serializer */
        $serializer = static::getContainer()->get(SerializerInterface::class);
        $serializedCollection = $serializer->serialize($dtoCollection, $format);

        /** @var Collection $deserializedCollection */
        $deserializedCollection = $serializer->deserialize($serializedCollection, Collection::class.'<'.PuppyDto::class.'>', $format);

        self::assertEquals(new Collection($dtoCollection, 3), $deserializedCollection);
        self::assertFalse($deserializedCollection->hasPagination());

        foreach ($deserializedCollection as $i => $item) {
            self::assertEquals($dtoCollection[$i], $item);
        }
    }

    public function formatsDataProvider(): iterable
    {
        yield ['jsonld'];
        yield ['jsonapi'];
        yield ['jsonhal'];
    }
}
