<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Entity;

use App\Cataloging\EntityInterface\CatalogSyndicationDestinationEntityInterface;
use App\Cataloging\ValueObject\CatalogSyndicationDestinationConfiguration;
use App\Cataloging\ValueObject\CatalogSyndicationDestinationDefinition;

/**
 * Represents the category syndication destination domain record.
 */
final readonly class CatalogSyndicationDestinationEntity implements CatalogSyndicationDestinationEntityInterface
{
    public function __construct(
        private CatalogSyndicationDestinationDefinition $definition,
        private CatalogSyndicationDestinationConfiguration $configuration,
        private string $createdBy,
        private \DateTimeImmutable $createdAt,
    ) {
    }

    /**
     * Registers a new syndication destination.
     */
    public static function register(
        CatalogSyndicationDestinationDefinition $definition,
        CatalogSyndicationDestinationConfiguration $configuration,
        string $createdBy,
    ): self {
        return new self(
            new CatalogSyndicationDestinationDefinition(
                trim($definition->destinationId()),
                trim($definition->name()),
                trim($definition->destinationType()),
                trim($definition->deliveryMode()),
            ),
            new CatalogSyndicationDestinationConfiguration(
                $configuration->enabled(),
                self::normalizeSettings($configuration->settings()),
            ),
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
            $normalizedKey = trim($key);
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

    public function destinationId(): string
    {
        return $this->definition->destinationId();
    }

    public function name(): string
    {
        return $this->definition->name();
    }

    public function destinationType(): string
    {
        return $this->definition->destinationType();
    }

    public function deliveryMode(): string
    {
        return $this->definition->deliveryMode();
    }

    public function enabled(): bool
    {
        return $this->configuration->enabled();
    }

    /** @return array<string,string> */
    public function settings(): array
    {
        /** @var array<string,string> $settings */
        $settings = $this->configuration->settings();

        return $settings;
    }

    public function createdBy(): string
    {
        return $this->createdBy;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
