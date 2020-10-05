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
        self::assertNotNull($route = $routes->get('test_check_resource'));

        self::assertSame('/test_check_resource', $route->getPath());
        self::assertSame(['POST'], $route->getMethods());
        self::assertSame(ApiResourceExistenceCheckerAction::class, $route->getDefault('_controller'));
        self::assertSame('', $route->getHost());
        self::assertEquals([], $route->getRequirements());

        $routes = (new RouteLoader('test', ['foo', 'bar']))();
        self::assertNotNull($route = $routes->get('test_check_resource'));

        self::assertSame('/test_check_resource', $route->getPath());
        self::assertSame(['POST'], $route->getMethods());
        self::assertSame(ApiResourceExistenceCheckerAction::class, $route->getDefault('_controller'));
        self::assertSame('{host}', $route->getHost());
        self::assertEquals(['host' => 'foo|bar'], $route->getRequirements());
    }
}
