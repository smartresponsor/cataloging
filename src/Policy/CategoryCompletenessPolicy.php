<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Policy;

use App\PolicyInterface\CategoryCompletenessPolicyInterface;

final class CategoryCompletenessPolicy implements CategoryCompletenessPolicyInterface
{
    public function buildChecks(array $payload): array
    {
        $seo = is_array($payload['seo'] ?? null) ? $payload['seo'] : [];
        $content = is_array($payload['content'] ?? null) ? $payload['content'] : [];
        $locale = is_array($payload['locale'] ?? null) ? $payload['locale'] : [];
        $media = is_array($payload['media'] ?? null) ? $payload['media'] : [];
        $presentation = is_array($payload['presentation'] ?? null) ? $payload['presentation'] : [];

        return [
            'slugReady' => '' !== trim((string) ($payload['slug'] ?? '')),
            'seoTitleReady' => '' !== trim((string) ($seo['title'] ?? '')),
            'seoDescriptionReady' => '' !== trim((string) ($seo['description'] ?? '')),
            'contentReady' => '' !== trim((string) ($content['body'] ?? '')),
            'localeCoverageReady' => count(array_filter((array) ($locale['enabled'] ?? []), static fn (mixed $value): bool => '' !== trim((string) $value))) > 0,
            'mediaReady' => '' !== trim((string) ($media['primaryAssetId'] ?? '')),
            'aliasReady' => count((array) ($payload['aliases'] ?? [])) > 0,
            'bannerReady' => '' !== trim((string) ($presentation['bannerId'] ?? '')),
            'htmlBlockReady' => '' !== trim((string) ($presentation['htmlBlockId'] ?? '')),
        ];
    }
}
