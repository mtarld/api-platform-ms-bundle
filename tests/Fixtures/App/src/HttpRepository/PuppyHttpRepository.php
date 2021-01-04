<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\HttpRepository;

use Mtarld\ApiPlatformMsBundle\HttpRepository\AbstractMicroserviceHttpRepository;
use Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\Dto\PuppyResourceDto;

/**
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class PuppyHttpRepository extends AbstractMicroserviceHttpRepository
{
    protected function getMicroserviceName(): string
    {
        return 'bar';
    }

    protected function getResourceEndpoint(): string
    {
        return 'puppies';
    }

    protected function getResourceDto(): string
    {
        return PuppyResourceDto::class;
    }
}
