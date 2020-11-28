<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\Authentication;

use Mtarld\ApiPlatformMsBundle\HttpClient\AuthenticationHeaderProviderInterface;
use Mtarld\ApiPlatformMsBundle\Microservice\Microservice;

/**
 * @author Smaïne Milianni <smaine.milianni@gmail.com>
 */
class BasicAuthenticationHeaderProvider implements AuthenticationHeaderProviderInterface
{
    private $enabled = false;

    public function getHeader(): string
    {
        return 'Authorization';
    }

    public function getValue(): string
    {
        return 'Basic password';
    }

    public function supports(array $context): bool
    {
        /** @var Microservice $ms */
        $ms = $context['microservice'];

        return $this->enabled && '/api/puppies' === $context['uri'] && 'GET' === $context['method'] && 'bar' == $ms->getName();
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }
}
