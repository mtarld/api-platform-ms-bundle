<?php

declare(strict_types=1);

namespace Mtarld\ApiPlatformMsBundle\Tests\Validator;

use Mtarld\ApiPlatformMsBundle\ApiResource\ExistenceVerifier;
use Mtarld\ApiPlatformMsBundle\Validator\ApiResourceExists;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ApiResourceExistsValidatorTest extends KernelTestCase
{
    /**
     * @dataProvider provideBlankIris
     */
    public function testDoNothingWhenNullOrEmpty(?string $iri): void
    {
        $validator = self::getContainer()->get(ValidatorInterface::class);
        $existenceVerifier = $this->createMock(ExistenceVerifier::class);
        $existenceVerifier->expects(self::never())->method('verify');
        self::getContainer()->set('test.existence_verifier', $existenceVerifier);

        $violations = $validator->validate($iri, new ApiResourceExists('bar'));

        self::assertCount(0, $violations);
    }

    public static function provideBlankIris(): iterable
    {
        yield 'null' => [null];
        yield 'empty' => [''];
    }

    /**
     * @dataProvider provideInvalidIris
     */
    public function testDoNotCallRemoteWhenInvalidIri(int|string|null $invalidIri): void
    {
        $existenceVerifier = $this->createMock(ExistenceVerifier::class);
        $existenceVerifier->expects(self::never())->method('verify');

        self::getContainer()->set('test.existence_verifier', $existenceVerifier);
        self::getContainer()->get(ValidatorInterface::class)->validate($invalidIri, new ApiResourceExists('bar'));
    }

    public static function provideInvalidIris(): iterable
    {
        yield ['   '];
        yield ['/api'];
        yield [2];
    }

    /**
     * @dataProvider provideInvalidIris
     */
    public function testViolationsWhenInvalidIri(int|string|null $invalidIri): void
    {
        $validator = self::getContainer()->get(ValidatorInterface::class);
        $violations = $validator->validate($invalidIri, new ApiResourceExists('bar'));

        self::assertCount(1, $violations);
        self::assertSame($invalidIri, $violations->get(0)->getInvalidValue());
        self::assertSame(Regex::REGEX_FAILED_ERROR, $violations->get(0)->getCode());
    }

    public function testViolationsWhenApiResourceNotFound(): void
    {
        $existenceVerifier = $this->createMock(ExistenceVerifier::class);
        $existenceVerifier->method('verify')->willReturn(false);
        self::getContainer()->set('test.existence_verifier', $existenceVerifier);

        $validator = self::getContainer()->get(ValidatorInterface::class);
        $violations = $validator->validate('/api/products/1', new ApiResourceExists('bar'));

        self::assertCount(1, $violations);
        self::assertSame("'/api/products/1' does not exist in microservice 'bar'", $violations->get(0)->getMessage());
        self::assertSame('/api/products/1', $violations->get(0)->getInvalidValue());
        self::assertSame(ApiResourceExists::IRI_NOT_FOUND_ERROR, $violations->get(0)->getCode());
    }

    public function testLogOnHttpException(): void
    {
        $httpClient = new MockHttpClient([
            new MockResponse(info: ['http_code' => 500]),
        ]);
        self::getContainer()->set('test.http_client', $httpClient);

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects(self::once())
            ->method('debug')
            ->with("Unable to validate IRIs of microservice 'bar': HTTP 500 returned for \"https://localhost/api/products/1\".")
        ;
        self::getContainer()->set('test.logger', $logger);

        self::getContainer()->get(ValidatorInterface::class)->validate(
            '/api/products/1',
            new ApiResourceExists('bar', true),
        );
    }

    public function testSkipOnError(): void
    {
        $httpClient = new MockHttpClient([
            new MockResponse(info: ['http_code' => 500]),
            new MockResponse(info: ['http_code' => 500]),
        ]);
        self::getContainer()->set('test.http_client', $httpClient);

        $violations = self::getContainer()->get(ValidatorInterface::class)->validate(
            '/api/products/1',
            new ApiResourceExists('bar', true)
        );
        self::assertCount(0, $violations);

        $this->expectException(\RuntimeException::class);
        self::getContainer()->get(ValidatorInterface::class)->validate(
            '/api/products/1',
            new ApiResourceExists('bar')
        );
    }
}
