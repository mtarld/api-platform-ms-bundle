<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\Validator;

use Mtarld\ApiPlatformMsBundle\ApiResource\ExistenceChecker;
use Mtarld\ApiPlatformMsBundle\Validator\ApiResourceExist;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\Validator\Exception\InvalidOptionsException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use TypeError;

/**
 * @group resource-existence
 * @group validator
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class ApiResourceExistValidatorTest extends KernelTestCase
{
    protected function setUp(): void
    {
        static::bootKernel();
    }

    public function testMissingMicroserviceOption(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage(sprintf('The options "value" do not exist in constraint "%s"', ApiResourceExist::class));

        new ApiResourceExist();
    }

    public function testInvalidMicroserviceOption(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(sprintf('"%s::__construct()": Expected argument $microservice to be a string, got "int".', ApiResourceExist::class));

        new ApiResourceExist(1);
    }

    public function testDoNothingWhenNull(): void
    {
        $existenceChecker = $this->createMock(ExistenceChecker::class);
        $existenceChecker
            ->expects(self::never())
            ->method('getExistenceStatuses')
        ;

        static::getContainer()->set('test.existence_checker', $existenceChecker);
        static::getContainer()->get(ValidatorInterface::class)->validate(null, new ApiResourceExist('foo'));
    }

    public function testViolationsWhenNotExists(): void
    {
        $httpClient = new MockHttpClient([
            new MockResponse('{"existences": {"1": true, "2": false}}'),
        ]);
        static::getContainer()->set('test.http_client', $httpClient);

        $violations = static::getContainer()->get(ValidatorInterface::class)->validate([1, 2], new ApiResourceExist('bar'));
        self::assertCount(1, $violations);
        self::assertSame("'2' does not exist in microservice 'bar'.", $violations->get(0)->getMessage());
    }

    public function testLogOnHttpException(): void
    {
        $httpClient = new MockHttpClient([
            new MockResponse('', ['http_code' => 500]),
        ]);
        static::getContainer()->set('test.http_client', $httpClient);

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects(self::once())
            ->method('debug')
            ->with("Unable to validate IRIs of microservice 'bar': HTTP 500 returned for \"https://localhost/api/bar_check_resource\".")
        ;
        static::getContainer()->set('test.logger', $logger);

        static::getContainer()->get(ValidatorInterface::class)->validate([1, 2], new ApiResourceExist('bar', true));
    }

    public function testOnWronglyFormattedResponse(): void
    {
        $httpClient = new MockHttpClient([
            new MockResponse('{}', ['http_code' => 200]),
        ]);
        static::getContainer()->set('test.http_client', $httpClient);

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects(self::once())
            ->method('debug')
        ;
        static::getContainer()->set('test.logger', $logger);

        static::getContainer()->get(ValidatorInterface::class)->validate([1, 2], new ApiResourceExist('bar', true));
    }

    public function testSkipOnError(): void
    {
        $httpClient = new MockHttpClient([
            new MockResponse('', ['http_code' => 500]),
            new MockResponse('', ['http_code' => 500]),
        ]);
        static::getContainer()->set('test.http_client', $httpClient);

        $violations = static::getContainer()->get(ValidatorInterface::class)->validate([1, 2], new ApiResourceExist('bar', true));
        self::assertCount(0, $violations);

        $this->expectException(RuntimeException::class);
        static::getContainer()->get(ValidatorInterface::class)->validate([1, 2], new ApiResourceExist('bar'));
    }

    /**
     * @dataProvider php8AndDoctrineConstraintsDataProvider
     */
    public function testPhp8AndDoctrineConstraints(ApiResourceExist $constraint): void
    {
        $existenceChecker = $this->createMock(ExistenceChecker::class);
        $existenceChecker
            ->expects(self::once())
            ->method('getExistenceStatuses')
        ;

        static::getContainer()->set('test.existence_checker', $existenceChecker);
        static::getContainer()->get(ValidatorInterface::class)->validate([1], $constraint);
    }

    public function php8AndDoctrineConstraintsDataProvider(): iterable
    {
        yield 'Doctrine annotations' => [new ApiResourceExist(['microservice' => 'foo'])];
        yield 'named parameters' => [eval('return new Mtarld\ApiPlatformMsBundle\Validator\ApiResourceExist(microservice: "foo");')];
    }
}
