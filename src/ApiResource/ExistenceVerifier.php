<?php

namespace Mtarld\ApiPlatformMsBundle\ApiResource;

use Mtarld\ApiPlatformMsBundle\HttpClient\GenericHttpClient;
use Mtarld\ApiPlatformMsBundle\HttpClient\ReplaceableHttpClientInterface;
use Mtarld\ApiPlatformMsBundle\HttpClient\ReplaceableHttpClientTrait;
use Mtarld\ApiPlatformMsBundle\Microservice\MicroservicePool;

/**
 * @final @internal
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class ExistenceVerifier implements ReplaceableHttpClientInterface
{
    use ReplaceableHttpClientTrait;

    public function __construct(
        private GenericHttpClient $httpClient,
        private readonly MicroservicePool $microservices,
    ) {
    }

    public function verify(string $microserviceName, string $iri): bool
    {
        $response = $this->httpClient->request(
            $this->microservices->get($microserviceName),
            'GET',
            $iri,
        );

        $statusCode = $response->getStatusCode();

        if (200 <= $statusCode && 300 > $statusCode) {
            return true;
        }

        if (404 === $statusCode) {
            return false;
        }

        // make it throw
        return (bool) $response->getContent(true);
    }
}
