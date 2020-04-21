<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\Serializer;

use stdClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * @group functional
 * @group denormalizer
 * @group wip
 */
class ConstraintViolationListDenormalizerTest extends KernelTestCase
{
    public function setUp(): void
    {
        static::bootKernel();
    }

    /**
     * @dataProvider denormalizationDataProvider
     * @testdox Can denormalize with $format format
     */
    public function testDenormalization(string $format): void
    {
        $root = new stdClass();

        $violations = new ConstraintViolationList([
            new ConstraintViolation('This is a violation!', null, [], $root, 'property', null),
            new ConstraintViolation('This is a violation as well!', null, [], $root, 'anotherProperty', null),
        ]);

        /** @var SerializerInterface $serializer */
        $serializer = static::$container->get(SerializerInterface::class);
        $serializedViolations = $serializer->serialize($violations, $format);

        $this->assertEquals(
            new ConstraintViolationList([
                new ConstraintViolation('This is a violation!', null, [], null, 'property', null),
                new ConstraintViolation('This is a violation as well!', null, [], null, 'anotherProperty', null),
            ]),
            $serializer->deserialize($serializedViolations, ConstraintViolationList::class, $format)
        );
    }

    public function denormalizationDataProvider(): iterable
    {
        yield ['json'];
        yield ['jsonld'];
        yield ['jsonapi'];
        yield ['jsonhal'];
    }
}
