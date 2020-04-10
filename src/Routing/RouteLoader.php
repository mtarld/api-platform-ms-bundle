<?php

namespace Mtarld\ApiPlatformMsBundle\Routing;

use Mtarld\ApiPlatformMsBundle\Controller\ResourceExistenceCheckerAction;
use Symfony\Bundle\FrameworkBundle\Routing\RouteLoaderInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * @internal
 */
class RouteLoader implements RouteLoaderInterface
{
    private $name;
    private $hosts;

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
            ['_controller' => ResourceExistenceCheckerAction::class],
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
