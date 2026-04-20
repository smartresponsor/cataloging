<?php

declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/**
 * Value object describing category merch banner publication input.
 */
final readonly class CategoryMerchBannerPublishRequest
{
    public function __construct(
        public string $categoryId,
        public string $title,
        public string $content,
    ) {
    }
}
