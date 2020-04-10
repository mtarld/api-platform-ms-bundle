<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\Routing;

use Mtarld\ApiPlatformMsBundle\Controller\ApiResourceExistenceCheckerAction;
use Mtarld\ApiPlatformMsBundle\Routing\RouteLoader;
use PHPUnit\Framework\TestCase;

/**
 * @group routing
 */
class RouteLoaderTest extends TestCase
{
    public function testResourceExistenceCheckerRoute(): void
    {
        $routes = (new RouteLoader('test'))();
        $this->assertNotNull($route = $routes->get('test_check_resource'));

        $this->assertSame('/test_check_resource', $route->getPath());
        $this->assertSame(['POST'], $route->getMethods());
        $this->assertSame(ApiResourceExistenceCheckerAction::class, $route->getDefault('_controller'));
        $this->assertSame('', $route->getHost());
        $this->assertEquals([], $route->getRequirements());

        $routes = (new RouteLoader('test', ['foo', 'bar']))();
        $this->assertNotNull($route = $routes->get('test_check_resource'));

        $this->assertSame('/test_check_resource', $route->getPath());
        $this->assertSame(['POST'], $route->getMethods());
        $this->assertSame(ApiResourceExistenceCheckerAction::class, $route->getDefault('_controller'));
        $this->assertSame('{host}', $route->getHost());
        $this->assertEquals(['host' => 'foo|bar'], $route->getRequirements());
    }
}
