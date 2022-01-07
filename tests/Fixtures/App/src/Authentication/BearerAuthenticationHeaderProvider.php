<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\Authentication;

use Mtarld\ApiPlatformMsBundle\HttpClient\AuthenticationHeaderProviderInterface;

/**
 * @author Smaïne Milianni <smaine.milianni@gmail.com>
 */
class BearerAuthenticationHeaderProvider implements AuthenticationHeaderProviderInterface
{
    private bool $enabled = false;

    public function getHeader(): string
    {
        return 'Authorization';
    }

    public function getValue(): string
    {
        return 'Bearer bearer';
    }

    public function supports(array $context): bool
    {
        return $this->enabled && '/api/dummies' === $context['uri'] && 'DELETE' === $context['method'];
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }
}
