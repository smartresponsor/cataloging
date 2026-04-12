<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Policy;

use App\PolicyInterface\CategoryCompletenessPolicyInterface;
use App\Service\CategoryPayloadValueNormalizer;

/**
 * Provides the category completeness policy implementation.
 */
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
            'slugReady' => '' !== CategoryPayloadValueNormalizer::scalarString($payload['slug'] ?? null),
            'seoTitleReady' => '' !== CategoryPayloadValueNormalizer::scalarString($seo['title'] ?? null),
            'seoDescriptionReady' => '' !== CategoryPayloadValueNormalizer::scalarString($seo['description'] ?? null),
            'contentReady' => '' !== CategoryPayloadValueNormalizer::scalarString($content['body'] ?? null),
            'localeCoverageReady' => count(array_filter(CategoryPayloadValueNormalizer::stringList($locale['enabled'] ?? null))) > 0,
            'mediaReady' => '' !== CategoryPayloadValueNormalizer::scalarString($media['primaryAssetId'] ?? null),
            'aliasReady' => is_array($payload['aliases'] ?? null) && [] !== $payload['aliases'],
            'bannerReady' => '' !== CategoryPayloadValueNormalizer::scalarString($presentation['bannerId'] ?? null),
            'htmlBlockReady' => '' !== CategoryPayloadValueNormalizer::scalarString($presentation['htmlBlockId'] ?? null),
        ];
    }
}
