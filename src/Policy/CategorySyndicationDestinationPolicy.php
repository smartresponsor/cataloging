<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

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

    /**
     * @param array<string,mixed> $settings
     *
     * @return array<string,string>
     */
    public function normalizeSettings(array $settings): array
    {
        $normalized = [];
        foreach ($settings as $key => $value) {
            $normalizedKey = trim((string) $key);
            if ('' === $normalizedKey) {
                continue;
            }
            if (is_array($value)) {
                $normalized[$normalizedKey] = implode(',', $this->stringList($value));
                continue;
            }
            if (is_bool($value)) {
                $normalized[$normalizedKey] = $value ? 'true' : 'false';
                continue;
            }
            $normalized[$normalizedKey] = is_scalar($value) ? trim((string) $value) : '';
        }
        ksort($normalized);

        return $normalized;
    }

    /**
     * @param array<mixed> $value
     *
     * @return list<string>
     */
    private function stringList(array $value): array
    {
        $result = [];
        foreach ($value as $item) {
            if (!is_scalar($item)) {
                continue;
            }
            $normalized = trim((string) $item);
            if ('' === $normalized) {
                continue;
            }
            $result[] = $normalized;
        }

        return array_values($result);
    }
}
