<?php

declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/**
 * Value object describing category merch pin creation input.
 */
final readonly class CategoryMerchPinCreateRequest
{
    public function __construct(
        public string $categoryId,
        public string $recordId,
        public int $position,
    ) {
    }
}
