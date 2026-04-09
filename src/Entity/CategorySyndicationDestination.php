<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Entity;

use App\EntityInterface\CategorySyndicationDestinationInterface;

/**
 * Represents the category syndication destination domain record.
 */
final class CategorySyndicationDestination implements CategorySyndicationDestinationInterface
{
    /**
     * @param array<string,string> $settings
     */
    public function __construct(
        private readonly string $destinationId,
        private readonly string $name,
        private readonly string $destinationType,
        private readonly string $deliveryMode,
        private readonly bool $enabled,
        private readonly array $settings,
        private readonly string $createdBy,
        private readonly \DateTimeImmutable $createdAt,
    ) {
    }

    /**
     * @param array<string,mixed> $settings
     */
    public static function register(
        string $destinationId,
        string $name,
        string $destinationType,
        string $deliveryMode,
        bool $enabled,
        array $settings,
        string $createdBy,
    ): self {
        return new self(
            trim($destinationId),
            trim($name),
            trim($destinationType),
            trim($deliveryMode),
            $enabled,
            self::normalizeSettings($settings),
            trim($createdBy),
            new \DateTimeImmutable('now'),
        );
    }

    /**
     * @param array<string,mixed> $settings
     *
     * @return array<string,string>
     */
    private static function normalizeSettings(array $settings): array
    {
        $normalized = [];
        foreach ($settings as $key => $value) {
            if (!is_scalar($key)) {
                continue;
            }

            $normalizedKey = trim((string) $key);
            if ('' === $normalizedKey) {
                continue;
            }

            if (is_array($value)) {
                $parts = [];
                foreach ($value as $item) {
                    if (is_scalar($item)) {
                        $parts[] = trim((string) $item);
                    }
                }
                $normalized[$normalizedKey] = implode(
                    ',',
                    array_values(array_filter($parts, static fn (string $part): bool => '' !== $part)),
                );
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
     * Handles the destination id workflow.
     */
    public function destinationId(): string
    {
        return $this->destinationId;
    }

    /**
     * Handles the name workflow.
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Handles the destination type workflow.
     */
    public function destinationType(): string
    {
        return $this->destinationType;
    }

    /**
     * Handles the delivery mode workflow.
     */
    public function deliveryMode(): string
    {
        return $this->deliveryMode;
    }

    /**
     * Handles the enabled workflow.
     */
    public function enabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Updates the tings value.
     */
    public function settings(): array
    {
        return $this->settings;
    }

    /**
     * Creates the d by result for the current workflow.
     */
    public function createdBy(): string
    {
        return $this->createdBy;
    }

    /**
     * Creates the d at result for the current workflow.
     */
    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
