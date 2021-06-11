<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\EventListener;

use Mtarld\ApiPlatformMsBundle\Event\RequestEvent;
use Mtarld\ApiPlatformMsBundle\EventListener\RequestLoggerListener;
use Mtarld\ApiPlatformMsBundle\Microservice\Microservice;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class RequestLoggerListenerTest extends TestCase
{
    /**
     * @dataProvider microserviceDataProvider
     */
    public function testRequestIsLoggedViaAnEvent(Microservice $microservice, string $microserviceName, string $method, string $uri)
    {
        $event = new RequestEvent($microservice, $method, $uri, []);
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())
            ->method('debug')
            ->with('Calling "{microservice_name}" microservice: "{method} {url}".', [
                'method' => $method,
                'microservice_name' => $microserviceName,
                'url' => $uri,
            ])
        ;

        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber(new RequestLoggerListener($logger));
        $dispatcher->dispatch($event);
    }

    public function microserviceDataProvider(): iterable
    {
        yield [new Microservice($msName = 'product', 'https://localhost', '/api', 'jsonld'),  $msName, 'GET', '/products'];
        yield [new Microservice($msName = 'user', 'https://domain', '/app', 'json'), $msName, 'DELETE', '/users'];
    }
}
