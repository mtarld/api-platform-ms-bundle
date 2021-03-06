<?php

namespace Mtarld\ApiPlatformMsBundle\Routing;

use Mtarld\ApiPlatformMsBundle\Controller\ApiResourceExistenceCheckerAction;
use Symfony\Bundle\FrameworkBundle\Routing\RouteLoaderInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

// Help opcache.preload discover always-needed symbols
class_exists(ApiResourceExistenceCheckerAction::class);
class_exists(Route::class);
class_exists(RouteCollection::class);

/**
 * @final @internal
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class RouteLoader implements RouteLoaderInterface
{
    private $name;
    private $hosts;

    /**
     * @param array<string> $hosts
     */
    public function __construct(string $name, array $hosts = [])
    {
        $this->name = $name;
        $this->hosts = $hosts;
    }

    public function __invoke(): RouteCollection
    {
        $routes = new RouteCollection();
        $iriExistRoute = new Route(
            sprintf('/%s_check_resource', $this->name),
            ['_controller' => ApiResourceExistenceCheckerAction::class],
            [],
            [],
            '',
            [],
            ['POST']
        );

        if (0 < count($this->hosts)) {
            $iriExistRoute
                ->setRequirements([
                    'host' => implode('|', $this->hosts),
                ])
                ->setHost('{host}')
            ;
        }

        $routes->add(sprintf('%s_check_resource', $this->name), $iriExistRoute);

        return $routes;
    }
}
