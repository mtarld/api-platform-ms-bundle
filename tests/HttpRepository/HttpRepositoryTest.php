<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\HttpRepository;

use Mtarld\ApiPlatformMsBundle\Exception\ResourceValidationException;
use Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\Dto\PuppyResourceDto;
use Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\Entity\Puppy;
use Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\HttpRepository\PuppyHttpRepository;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @group repository
 * @group http
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class HttpRepositoryTest extends KernelTestCase
{
    public function setUp(): void
    {
        static::bootKernel();
    }

    public function testFindExistingResourceByIri(): void
    {
        /** @var SerializerInterface $serializer */
        $serializer = static::$container->get(SerializerInterface::class);

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->method('getContent')
            ->willReturn($serializer->serialize(new Puppy(1, 'foo'), 'jsonld'))
        ;

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(self::once())
            ->method('request')
            ->with(
                'GET',
                '/api/puppies/1',
                [
                    'base_uri' => 'https://localhost',
                    'headers' => [
                        'Content-Type' => 'application/ld+json',
                        'Accept' => 'application/ld+json',
                    ],
                ]
            )
            ->willReturn($response)
        ;

        static::$container->set('test.http_client', $httpClient);

        /** @var PuppyHttpRepository $httpRepository */
        $httpRepository = static::$container->get(PuppyHttpRepository::class);

        $puppyDto = $httpRepository->fetchOneByIri('/puppies/1');
        self::assertEquals(new PuppyResourceDto('/puppies/1', 'foo'), $puppyDto);
    }

    public function testFindMissingResourceByIri(): void
    {
        $httpClient = new MockHttpClient([
            new MockResponse('', ['http_code' => 404]),
        ]);

        static::$container->set('test.http_client', $httpClient);

        /** @var PuppyHttpRepository $httpRepository */
        $httpRepository = static::$container->get(PuppyHttpRepository::class);

        $puppyDto = $httpRepository->fetchOneByIri('/puppies/1');
        self::assertNull($puppyDto);
    }

    public function testFindResourceBy(): void
    {
        /** @var SerializerInterface $serializer */
        $serializer = static::$container->get(SerializerInterface::class);

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->method('getContent')
            ->willReturn($serializer->serialize([new Puppy(1, 'foo'), new Puppy(2, 'bar')], 'jsonld'))
        ;

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(self::once())
            ->method('request')
            ->with(
                'GET',
                '/api/puppies?superName%5B0%5D=foo&superName%5B1%5D=bar&groups%5B0%5D=translations',
                [
                    'base_uri' => 'https://localhost',
                    'headers' => [
                        'Content-Type' => 'application/ld+json',
                        'Accept' => 'application/ld+json',
                    ],
                ]
            )
            ->willReturn($response)
        ;

        static::$container->set('test.http_client', $httpClient);

        /** @var PuppyHttpRepository $httpRepository */
        $httpRepository = static::$container->get(PuppyHttpRepository::class);

        $puppyDtos = $httpRepository->fetchBy(['superName' => ['foo', 'bar']], ['groups' => ['translations']]);
        self::assertCount(2, $puppyDtos);
        self::assertNotNull($puppyDtos->getMicroservice());
    }

    public function testFindOneExistingResourceBy(): void
    {
        /** @var SerializerInterface $serializer */
        $serializer = static::$container->get(SerializerInterface::class);

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->method('getContent')
            ->willReturn($serializer->serialize([new Puppy(1, 'foo')], 'jsonld'))
        ;

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(self::once())
            ->method('request')
            ->with(
                'GET',
                '/api/puppies?superName=foo',
                [
                    'base_uri' => 'https://localhost',
                    'headers' => [
                        'Content-Type' => 'application/ld+json',
                        'Accept' => 'application/ld+json',
                    ],
                ]
            )
            ->willReturn($response)
        ;

        static::$container->set('test.http_client', $httpClient);

        /** @var PuppyHttpRepository $httpRepository */
        $httpRepository = static::$container->get(PuppyHttpRepository::class);

        $puppyDto = $httpRepository->fetchOneBy(['superName' => 'foo']);
        self::assertEquals(new PuppyResourceDto('/puppies/1', 'foo'), $puppyDto);
    }

    public function testFindOneMissingResourceBy(): void
    {
        /** @var SerializerInterface $serializer */
        $serializer = static::$container->get(SerializerInterface::class);

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->method('getContent')
            ->willReturn($serializer->serialize([], 'jsonld'))
        ;

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(self::once())
            ->method('request')
            ->with(
                'GET',
                '/api/puppies?superName%5B0%5D=foo&superName%5B1%5D=bar',
                [
                    'base_uri' => 'https://localhost',
                    'headers' => [
                        'Content-Type' => 'application/ld+json',
                        'Accept' => 'application/ld+json',
                    ],
                ]
            )
            ->willReturn($response)
        ;

        static::$container->set('test.http_client', $httpClient);

        /** @var PuppyHttpRepository $httpRepository */
        $httpRepository = static::$container->get(PuppyHttpRepository::class);

        $puppyDto = $httpRepository->fetchOneBy(['superName' => ['foo', 'bar']]);
        self::assertNull($puppyDto);
    }

    public function testFindAllResources(): void
    {
        /** @var SerializerInterface $serializer */
        $serializer = static::$container->get(SerializerInterface::class);

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->method('getContent')
            ->willReturn($serializer->serialize([new Puppy(1, 'foo'), new Puppy(2, 'bar')], 'jsonld'))
        ;

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(self::once())
            ->method('request')
            ->with(
                'GET',
                '/api/puppies?itemsPerPage=2',
                [
                    'base_uri' => 'https://localhost',
                    'headers' => [
                        'Content-Type' => 'application/ld+json',
                        'Accept' => 'application/ld+json',
                    ],
                ]
            )
            ->willReturn($response)
        ;

        static::$container->set('test.http_client', $httpClient);

        /** @var PuppyHttpRepository $httpRepository */
        $httpRepository = static::$container->get(PuppyHttpRepository::class);

        $puppyDtos = $httpRepository->fetchAll(['itemsPerPage' => 2]);
        self::assertCount(2, $puppyDtos);
        self::assertNotNull($puppyDtos->getMicroservice());
    }

    public function testSwitchHttpClient(): void
    {
        /** @var SerializerInterface $serializer */
        $serializer = static::$container->get(SerializerInterface::class);

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->method('getContent')
            ->willReturn($serializer->serialize(new Puppy(1, 'foo'), 'jsonld'))
        ;

        $firstHttpClient = $this->createMock(HttpClientInterface::class);
        $firstHttpClient
            ->expects(self::once())
            ->method('request')
            ->willReturn($response)
        ;

        $secondHttpClient = $this->createMock(HttpClientInterface::class);
        $secondHttpClient
            ->expects(self::once())
            ->method('request')
            ->willReturn($response)
        ;

        static::$container->set('test.http_client', $firstHttpClient);

        /** @var PuppyHttpRepository $httpRepository */
        $httpRepository = static::$container->get(PuppyHttpRepository::class);

        $puppyDto = $httpRepository->fetchOneByIri('/puppies/1');
        self::assertEquals(new PuppyResourceDto('/puppies/1', 'foo'), $puppyDto);

        $httpRepository->setWrappedHttpClient($secondHttpClient);

        $puppyDto = $httpRepository->fetchOneByIri('/puppies/1');
        self::assertEquals(new PuppyResourceDto('/puppies/1', 'foo'), $puppyDto);
    }

    public function testCreateResource(): void
    {
        /** @var SerializerInterface $serializer */
        $serializer = static::$container->get(SerializerInterface::class);

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->method('getContent')
            ->willReturn($serializer->serialize(new Puppy(1, 'foo'), 'jsonld'))
        ;

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(self::once())
            ->method('request')
            ->with(
                'POST',
                '/api/puppies?user=me',
                [
                    'base_uri' => 'https://localhost',
                    'headers' => [
                        'Content-Type' => 'application/ld+json',
                        'Accept' => 'application/ld+json',
                    ],
                    'body' => '{"iri":null,"super_name":"foo","color":null,"hairs":[]}',
                ]
            )
            ->willReturn($response)
        ;

        static::$container->set('test.http_client', $httpClient);

        /** @var PuppyHttpRepository $httpRepository */
        $httpRepository = static::$container->get(PuppyHttpRepository::class);

        $createdPuppyDto = $httpRepository->create(new PuppyResourceDto(null, 'foo'), ['user' => 'me']);
        self::assertEquals(new PuppyResourceDto('/puppies/1', 'foo'), $createdPuppyDto);
    }

    public function testCreateResourceWithViolations(): void
    {
        $this->expectException(ResourceValidationException::class);

        /** @var SerializerInterface $serializer */
        $serializer = static::$container->get(SerializerInterface::class);

        $httpClient = new MockHttpClient([
            new MockResponse(
                $serializer->serialize(new ConstraintViolationList([
                    new ConstraintViolation('This is a violation', null, [], new Puppy(1, 'foo'), 'superName', null),
                ]), 'jsonld'),
                ['http_code' => 400]
            ),
        ]);

        static::$container->set('test.http_client', $httpClient);

        /** @var PuppyHttpRepository $httpRepository */
        $httpRepository = static::$container->get(PuppyHttpRepository::class);
        $httpRepository->create(new PuppyResourceDto(null, 'foo'));
    }

    public function testUpdateResource(): void
    {
        /** @var SerializerInterface $serializer */
        $serializer = static::$container->get(SerializerInterface::class);

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->method('getContent')
            ->willReturn($serializer->serialize(new Puppy(1, 'foo'), 'jsonld'))
        ;

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(self::once())
            ->method('request')
            ->with(
                'PUT',
                '/api/puppies/1?user=me',
                [
                    'base_uri' => 'https://localhost',
                    'headers' => [
                        'Content-Type' => 'application/ld+json',
                        'Accept' => 'application/ld+json',
                    ],
                    'body' => '{"iri":"\/puppies\/1","super_name":"foo","color":null,"hairs":[]}',
                ]
            )
            ->willReturn($response)
        ;

        static::$container->set('test.http_client', $httpClient);

        /** @var PuppyHttpRepository $httpRepository */
        $httpRepository = static::$container->get(PuppyHttpRepository::class);

        $createdPuppyDto = $httpRepository->update(new PuppyResourceDto('/puppies/1', 'foo'), ['user' => 'me']);
        self::assertEquals(new PuppyResourceDto('/puppies/1', 'foo'), $createdPuppyDto);
    }

    public function testUpdateResourceWithViolations(): void
    {
        $this->expectException(ResourceValidationException::class);

        /** @var SerializerInterface $serializer */
        $serializer = static::$container->get(SerializerInterface::class);

        $httpClient = new MockHttpClient([
            new MockResponse(
                $serializer->serialize(new ConstraintViolationList([
                    new ConstraintViolation('This is a violation', null, [], new Puppy(1, 'foo'), 'superName', null),
                ]), 'jsonld'),
                ['http_code' => 400]
            ),
        ]);

        static::$container->set('test.http_client', $httpClient);

        /** @var PuppyHttpRepository $httpRepository */
        $httpRepository = static::$container->get(PuppyHttpRepository::class);
        $httpRepository->update(new PuppyResourceDto('/puppies/1', 'foo'));
    }

    public function testUpdateResourceWithoutIri(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot update a resource without iri');

        /** @var PuppyHttpRepository $httpRepository */
        $httpRepository = static::$container->get(PuppyHttpRepository::class);
        $httpRepository->update(new PuppyResourceDto(null, 'foo'));
    }

    public function testDeleteResource(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(self::once())
            ->method('request')
            ->with(
                'DELETE',
                '/api/puppies/1?user=me',
                [
                    'base_uri' => 'https://localhost',
                    'headers' => [
                        'Content-Type' => 'application/ld+json',
                        'Accept' => 'application/ld+json',
                    ],
                ]
            )
            ->willReturn($this->createMock(ResponseInterface::class))
        ;

        static::$container->set('test.http_client', $httpClient);

        /** @var PuppyHttpRepository $httpRepository */
        $httpRepository = static::$container->get(PuppyHttpRepository::class);

        $httpRepository->delete(new PuppyResourceDto('/puppies/1', 'foo'), ['user' => 'me']);
    }

    public function testDeleteResourceWithViolations(): void
    {
        $this->expectException(ResourceValidationException::class);

        /** @var SerializerInterface $serializer */
        $serializer = static::$container->get(SerializerInterface::class);

        $httpClient = new MockHttpClient([
            new MockResponse(
                $serializer->serialize(new ConstraintViolationList([
                    new ConstraintViolation('This is a violation', null, [], new Puppy(1, 'foo'), 'superName', null),
                ]), 'jsonld'),
                ['http_code' => 400]
            ),
        ]);

        static::$container->set('test.http_client', $httpClient);

        /** @var PuppyHttpRepository $httpRepository */
        $httpRepository = static::$container->get(PuppyHttpRepository::class);
        $httpRepository->delete(new PuppyResourceDto('/puppies/1', 'foo'));
    }

    public function testDeleteResourceWithoutIri(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot update a resource without iri');

        /** @var PuppyHttpRepository $httpRepository */
        $httpRepository = static::$container->get(PuppyHttpRepository::class);
        $httpRepository->delete(new PuppyResourceDto(null, 'foo'));
    }
}
