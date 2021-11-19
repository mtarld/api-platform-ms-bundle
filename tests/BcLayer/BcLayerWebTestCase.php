<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\BcLayer;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Kernel;

// Remove after support of symfony support of 4.4 is dropped
if (Kernel::VERSION_ID <= 50000) {
    class BcLayerWebTestCase extends BcLayerKernelTestCase
    {
    }
} else {
    class BcLayerWebTestCase extends WebTestCase
    {
    }
}
