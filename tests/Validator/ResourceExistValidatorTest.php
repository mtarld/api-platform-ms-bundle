<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\Validator;

use LogicException;
use Mtarld\ApiPlatformMsBundle\Resource\ExistenceChecker;
use Mtarld\ApiPlatformMsBundle\Validator\ResourceExist;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @group functional
 * @group resource-existence
 */
class ResourceExistValidatorTest extends KernelTestCase
{
    protected function setUp(): void
    {
        static::bootKernel();
    }

    public function testMissingMicroserviceOption(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("You must specify 'microservice' attribute of 'Mtarld\ApiPlatformMsBundle\Validator\ResourceExist'");

        static::$container->get(ValidatorInterface::class)->validate(1, new ResourceExist());
    }

    public function testDoNothingWhenNull(): void
    {
        $existenceChecker = $this->createMock(ExistenceChecker::class);
        $existenceChecker
            ->expects($this->never())
            ->method('getExistenceStatuses')
        ;

        static::$container->set('test.existence_checker', $existenceChecker);
        static::$container->get(ValidatorInterface::class)->validate(null, new ResourceExist([
            'microservice' => 'foo',
        ]));
    }

    public function testViolationsWhenNotExists(): void
    {
        $httpClient = new MockHttpClient([
            new MockResponse('{"existences": {"1": true, "2": false}}'),
        ]);
        static::$container->set('test.http_client', $httpClient);

        $violations = static::$container->get(ValidatorInterface::class)->validate([1, 2], new ResourceExist([
            'microservice' => 'bar',
        ]));
        $this->assertCount(1, $violations);
        $this->assertSame("'2' does not exist in microservice 'bar'.", $violations->get(0)->getMessage());
    }

    public function testLogOnHttpException(): void
    {
        $httpClient = new MockHttpClient([
            new MockResponse('', ['http_code' => 500]),
        ]);
        static::$container->set('test.http_client', $httpClient);

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('debug')
            ->with("Unable to validate IRIs of microservice 'bar': HTTP 500 returned for \"https://localhost/bar_check_resource\".")
        ;
        static::$container->set('test.logger', $logger);

        static::$container->get(ValidatorInterface::class)->validate([1, 2], new ResourceExist([
            'microservice' => 'bar',
            'skipOnError' => true,
        ]));
    }

    public function testOnWronglyFormattedResponse(): void
    {
        $httpClient = new MockHttpClient([
            new MockResponse('{}', ['http_code' => 200]),
        ]);
        static::$container->set('test.http_client', $httpClient);

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('debug')
        ;
        static::$container->set('test.logger', $logger);

        static::$container->get(ValidatorInterface::class)->validate([1, 2], new ResourceExist([
            'microservice' => 'bar',
            'skipOnError' => true,
        ]));
    }

    public function testSkipOnError(): void
    {
        $httpClient = new MockHttpClient([
            new MockResponse('', ['http_code' => 500]),
            new MockResponse('', ['http_code' => 500]),
        ]);
        static::$container->set('test.http_client', $httpClient);

        $violations = static::$container->get(ValidatorInterface::class)->validate([1, 2], new ResourceExist([
            'microservice' => 'bar',
            'skipOnError' => true,
        ]));
        $this->assertCount(0, $violations);

        $this->expectException(RuntimeException::class);
        static::$container->get(ValidatorInterface::class)->validate([1, 2], new ResourceExist([
            'microservice' => 'bar',
        ]));
    }
}
