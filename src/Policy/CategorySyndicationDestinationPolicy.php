<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Policy;

use App\PolicyInterface\CategorySyndicationDestinationPolicyInterface;

final class CategorySyndicationDestinationPolicy implements CategorySyndicationDestinationPolicyInterface
{
    private const TYPES = ['marketplace', 'storefront', 'search', 'feed', 'partner'];
    private const MODES = ['push', 'pull', 'export'];

    public function assertDestinationType(string $destinationType): void
    {
        if (!in_array(trim($destinationType), self::TYPES, true)) {
            throw new \InvalidArgumentException('Unsupported destination type.');
        }
    }

    public function assertDeliveryMode(string $deliveryMode): void
    {
        if (!in_array(trim($deliveryMode), self::MODES, true)) {
            throw new \InvalidArgumentException('Unsupported delivery mode.');
        }
    }

    public function normalizeSettings(array $settings): array
    {
        $normalized = [];
        foreach ($settings as $key => $value) {
            $normalizedKey = trim((string) $key);
            if (is_array($value)) {
                $normalized[$normalizedKey] = array_values(array_filter(
                    array_map(static fn (mixed $item): string => trim((string) $item), $value),
                    static fn (string $item): bool => '' !== $item,
                ));
                continue;
            }

            if (is_bool($value)) {
                $normalized[$normalizedKey] = $value ? 'true' : 'false';
                continue;
            }

            $normalized[$normalizedKey] = trim((string) $value);
        }

        ksort($normalized);

        return $normalized;
    }
}
