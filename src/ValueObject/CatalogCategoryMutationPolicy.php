<?php

declare(strict_types=1);

namespace App\ValueObject;

enum CatalogCategoryMutationPolicy: string
{
    case STRICT = 'strict';

    public static function fromString(string $value): self
    {
        $normalized = mb_strtolower(trim($value));

        return match ($normalized) {
            self::STRICT->value => self::STRICT,
            default => throw new \InvalidArgumentException(sprintf('Unsupported mutation policy "%s".', $value)),
        };
    }
}
