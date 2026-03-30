<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Policy;

use App\PolicyInterface\CategoryCompletenessPolicyInterface;

final class CategoryCompletenessPolicy implements CategoryCompletenessPolicyInterface
{
    /**
     * @param array<string,mixed> $payload
     *
     * @return array<string,bool>
     */
    public function buildChecks(array $payload): array
    {
        $seo = is_array($payload['seo'] ?? null) ? $payload['seo'] : [];
        $content = is_array($payload['content'] ?? null) ? $payload['content'] : [];
        $locale = is_array($payload['locale'] ?? null) ? $payload['locale'] : [];
        $media = is_array($payload['media'] ?? null) ? $payload['media'] : [];
        $presentation = is_array($payload['presentation'] ?? null) ? $payload['presentation'] : [];

        return [
            'slugReady' => '' !== $this->scalarString($payload['slug'] ?? null),
            'seoTitleReady' => '' !== $this->scalarString($seo['title'] ?? null),
            'seoDescriptionReady' => '' !== $this->scalarString($seo['description'] ?? null),
            'contentReady' => '' !== $this->scalarString($content['body'] ?? null),
            'localeCoverageReady' => count(array_filter($this->stringList($locale['enabled'] ?? null))) > 0,
            'mediaReady' => '' !== $this->scalarString($media['primaryAssetId'] ?? null),
            'aliasReady' => is_array($payload['aliases'] ?? null) && [] !== $payload['aliases'],
            'bannerReady' => '' !== $this->scalarString($presentation['bannerId'] ?? null),
            'htmlBlockReady' => '' !== $this->scalarString($presentation['htmlBlockId'] ?? null),
        ];
    }

    private function scalarString(mixed $value): string
    {
        return is_scalar($value) ? trim((string) $value) : '';
    }

    /** @return list<string> */
    private function stringList(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $result = [];
        foreach ($value as $item) {
            if (!is_scalar($item)) {
                continue;
            }
            $normalized = trim((string) $item);
            if ('' !== $normalized) {
                $result[] = $normalized;
            }
        }

        return array_values($result);
    }
}
