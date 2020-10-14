<?php

namespace Mtarld\ApiPlatformMsBundle\Serializer\JsonApi;

use Mtarld\ApiPlatformMsBundle\Serializer\AbstractApiResourceDenormalizer;

/**
 * @final @internal
 */
class ApiResourceDenormalizer extends AbstractApiResourceDenormalizer
{
    use JsonApiDenormalizerTrait;

    protected function getIri(array $data): string
    {
        return $data['data']['id'];
    }

    protected function prepareData(array $data): array
    {
        return $data['data']['attributes'];
    }

    /**
     * @psalm-param array{data: array<array-key, mixed>, included: array<array<array-key, mixed>>} $data
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    protected function prepareEmbeddedData(array $data): array
    {
        if (!isset($data['data']['relationships'], $data['included'])) {
            return $data;
        }

        $data['data']['attributes'] += $this->fillRelationships($data['data'], $this->indexEmbeddedElements($data));

        return $data;
    }

    private function indexEmbeddedElements(array $data): array
    {
        $indexedEmbeddedElements = [];

        foreach ($data['included'] as $embeddedElement) {
            $type = $embeddedElement['type'];
            $indexedEmbeddedElements[$type] = $indexedEmbeddedElements[$type] ?? [];
            $indexedEmbeddedElements[$type][$embeddedElement['id']] = $embeddedElement;
        }

        return $indexedEmbeddedElements;
    }

    private function fillRelationships(array $relationships, array $indexedEmbeddedElements): array
    {
        $result = [];

        foreach ($relationships['relationships'] ?? [] as $relationshipName => $relationship) {
            $relationshipData = $relationship['data'];

            // Collection
            if (!isset($relationshipData['type'])) {
                foreach ($relationshipData as $key => $relationshipItem) {
                    $result[$relationshipName] = $result[$relationshipName] ?? [];

                    if (null !== $includedElement = $indexedEmbeddedElements[$relationshipItem['type']][$relationshipItem['id']] ?? null) {
                        $result[$relationshipName][$key]['data'] = $includedElement;
                        $result[$relationshipName][$key]['data']['attributes'] += $this->fillRelationships($includedElement, $indexedEmbeddedElements);
                    }
                }

                continue;
            }

            // Single item
            $result[$relationshipName]['data'] = $indexedEmbeddedElements[$relationshipData['type']][$relationshipData['id']];
        }

        return $result;
    }
}
