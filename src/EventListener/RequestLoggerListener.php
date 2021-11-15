<?php

namespace Mtarld\ApiPlatformMsBundle\EventListener;

use Mtarld\ApiPlatformMsBundle\Event\RequestEvent;
use Psr\Log\LoggerInterface;

/**
 * @final
 *
 * @author Smaïne Milianni <smaine.milianni@gmail.com>
 */
class RequestLoggerListener
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(RequestEvent $event): void
    {
        $this->logger->debug('Calling "{microservice_name}" microservice: "{method} {url}".', [
            'method' => $event->getMethod(),
            'microservice_name' => $event->getMicroservice()->getName(),
            'url' => $event->getUri(),
        ]);
    }
}
