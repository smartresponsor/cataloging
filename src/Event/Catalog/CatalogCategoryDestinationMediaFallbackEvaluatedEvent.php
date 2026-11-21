<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Event\Catalog;

use App\Cataloging\EventInterface\Catalog\CatalogCategoryDestinationMediaFallbackEvaluatedEventInterface;

/**
 * Represents the category destination media fallback evaluated application event.
 */
final readonly class CatalogCategoryDestinationMediaFallbackEvaluatedEvent implements CatalogCategoryDestinationMediaFallbackEvaluatedEventInterface
{
    /**
     * @param list<string>       $requiredMissing
     * @param list<string>       $warnings
     * @param array<string,bool> $checks
     * @param list<string>       $exactMatchedBindingIds
     * @param list<string>       $fallbackMatchedBindingIds
     */
    public function __construct(
        private string $destinationId,
        private string $categoryId,
        private string $channel,
        private string $locale,
        private bool $publishable,
        private bool $publishableWithFallback,
        private array $requiredMissing,
        private array $warnings,
        private array $checks,
        private array $exactMatchedBindingIds,
        private array $fallbackMatchedBindingIds,
        private string $actorId,
        private string $reason,
        private \DateTimeImmutable $evaluatedAt,
    ) {
    }

    /**
     * Handles the payload workflow.
     */
    public function payload(): array
    {
        return [
            'destinationId' => $this->destinationId,
            'categoryId' => $this->categoryId,
            'channel' => $this->channel,
            'locale' => $this->locale,
            'publishable' => $this->publishable,
            'publishableWithFallback' => $this->publishableWithFallback,
            'requiredMissing' => $this->requiredMissing,
            'warnings' => $this->warnings,
            'checks' => $this->checks,
            'exactMatchedBindingIds' => $this->exactMatchedBindingIds,
            'fallbackMatchedBindingIds' => $this->fallbackMatchedBindingIds,
            'actorId' => $this->actorId,
            'reason' => $this->reason,
            'evaluatedAt' => $this->evaluatedAt->format(DATE_ATOM),
        ];
    }
}
