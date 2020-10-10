<?php

namespace Mtarld\ApiPlatformMsBundle\ApiResource;

use Mtarld\ApiPlatformMsBundle\Dto\ApiResourceExistenceCheckerPayload;
use Mtarld\ApiPlatformMsBundle\Dto\ApiResourceExistenceCheckerView;
use Mtarld\ApiPlatformMsBundle\HttpClient\GenericHttpClient;
use Mtarld\ApiPlatformMsBundle\HttpClient\ReplaceableHttpClientInterface;
use Mtarld\ApiPlatformMsBundle\HttpClient\ReplaceableHttpClientTrait;
use Mtarld\ApiPlatformMsBundle\Microservice\MicroservicePool;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;

/**
 * @final
 */
class ExistenceChecker implements ReplaceableHttpClientInterface
{
    use ReplaceableHttpClientTrait;

    private $httpClient;
    private $serializer;
    private $microservices;

    public function __construct(
        GenericHttpClient $httpClient,
        SerializerInterface $serializer,
        MicroservicePool $microservices
    ) {
        $this->httpClient = $httpClient;
        $this->serializer = $serializer;
        $this->microservices = $microservices;
    }

    /**
     * @param array<string> $iris
     *
     * @psalm-return array<array-key, bool>
     *
     * @throws ExceptionInterface
     */
    public function getExistenceStatuses(string $microserviceName, array $iris): array
    {
        if (empty($iris)) {
            return [];
        }

        $microservice = $this->microservices->get($microserviceName);

        $response = $this->httpClient->request(
            $microservice,
            'POST',
            sprintf('/%s_check_resource', $microserviceName),
            new ApiResourceExistenceCheckerPayload($iris),
            'application/json',
            'json'
        );

        /** @var ApiResourceExistenceCheckerView $checkedIris */
        $checkedIris = $this->serializer->deserialize($response->getContent(), ApiResourceExistenceCheckerView::class, 'json');

        return $checkedIris->existences;
    }
}
