<?php

namespace Mtarld\ApiPlatformMsBundle\HttpClient;

/**
 * @author Smaïne Milianni <smaine.milianni@gmail.com>
 */
interface AuthenticationHeaderProviderInterface
{
    public function getHeader(): string;

    public function getValue(): string;

    /**
     * @psalm-param array<array-key, mixed> $context
     */
    public function supports(array $context): bool;
}
