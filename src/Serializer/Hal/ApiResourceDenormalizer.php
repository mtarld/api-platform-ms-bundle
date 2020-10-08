<?php

namespace Mtarld\ApiPlatformMsBundle\Serializer\Hal;

use Mtarld\ApiPlatformMsBundle\Serializer\AbstractApiResourceDenormalizer;

/**
 * @final @internal
 */
class ApiResourceDenormalizer extends AbstractApiResourceDenormalizer
{
    use HalDenormalizerTrait;

    protected function getIri(array $data): string
    {
        return $data['_links']['self']['href'];
    }

    protected function prepareData(array $data): array
    {
        return $data;
    }

    protected function prepareEmbeddedData(array $data): array
    {
        if (!isset($data['_links'], $data['_embedded'])) {
            return $data;
        }

        return $this->fillRelationships($data, $this->indexEmbeddedElements($data['_embedded']));
    }

    private function indexEmbeddedElements(array $embeddedElements): array
    {
        $indexedEmbeddedElements = [];

        foreach ($embeddedElements as $embeddedElement) {
            // Collection
            if (!isset($embeddedElement['_links']['self']['href'])) {
                foreach ($embeddedElement as $embeddedElementItem) {
                    $itemLink = $embeddedElementItem['_links']['self']['href'];
                    $indexedEmbeddedElements[$itemLink] = $embeddedElementItem + ['iri' => $itemLink];
                    $indexedEmbeddedElements += $this->indexEmbeddedElements($embeddedElementItem['_embedded']);
                }

                continue;
            }

            // Single item
            if (!isset($indexedEmbeddedElements[$link = $embeddedElement['_links']['self']['href']])) {
                $indexedEmbeddedElements[$link] = $embeddedElement + ['iri' => $link];
            }
        }

        return $indexedEmbeddedElements;
    }

    private function fillRelationships(array $data, array $indexedEmbeddedElements): array
    {
        foreach ($data['_links'] as $linkName => $linkValue) {
            if ('self' === $linkName) {
                continue;
            }

            // Collection
            if (!isset($linkValue['href'])) {
                $data[$linkName] = array_map(function (array $item) use ($indexedEmbeddedElements): array {
                    return $this->fillRelationships($indexedEmbeddedElements[$item['href']], $indexedEmbeddedElements);
                }, $linkValue);

                continue;
            }

            // Single item
            $data[$linkName] = $indexedEmbeddedElements[$linkValue['href']];
        }

        return $data;
    }
}
