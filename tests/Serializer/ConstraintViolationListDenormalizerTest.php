<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\Serializer;

use Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\Entity\Puppy;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * @group denormalizer
 * @group constraint-violation-list
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class ConstraintViolationListDenormalizerTest extends KernelTestCase
{
    public function setUp(): void
    {
        static::bootKernel();
    }

    /**
     * @dataProvider formatsDataProvider
     * @testdox Can denormalize with $format format
     */
    public function testDenormalization(string $format): void
    {
        $root = new Puppy(1, 'foo');

        $violations = new ConstraintViolationList([
            new ConstraintViolation('This is a violation!', null, [], $root, 'property', null),
            new ConstraintViolation('This is a violation as well!', null, [], $root, 'anotherProperty', null),
        ]);

        /** @var SerializerInterface $serializer */
        $serializer = static::$container->get(SerializerInterface::class);
        $serializedViolations = $serializer->serialize($violations, $format);

        self::assertEquals(
            new ConstraintViolationList([
                new ConstraintViolation('This is a violation!', null, [], null, 'property', null),
                new ConstraintViolation('This is a violation as well!', null, [], null, 'anotherProperty', null),
            ]),
            $serializer->deserialize($serializedViolations, ConstraintViolationList::class, $format)
        );
    }

    public function formatsDataProvider(): iterable
    {
        yield ['jsonld'];
        yield ['jsonapi'];
        yield ['jsonhal'];
    }
}
