<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Event;

use App\EventInterface\CategoryDestinationMediaFallbackEvaluatedInterface;

final class CategoryDestinationMediaFallbackEvaluated implements CategoryDestinationMediaFallbackEvaluatedInterface
{
    /**
     * @param list<string>       $requiredMissing
     * @param list<string>       $warnings
     * @param array<string,bool> $checks
     * @param list<string>       $exactMatchedBindingIds
     * @param list<string>       $fallbackMatchedBindingIds
     */
    public function __construct(
        private readonly string $destinationId,
        private readonly string $categoryId,
        private readonly string $channel,
        private readonly string $locale,
        private readonly bool $publishable,
        private readonly bool $publishableWithFallback,
        private readonly array $requiredMissing,
        private readonly array $warnings,
        private readonly array $checks,
        private readonly array $exactMatchedBindingIds,
        private readonly array $fallbackMatchedBindingIds,
        private readonly string $actorId,
        private readonly string $reason,
        private readonly \DateTimeImmutable $evaluatedAt,
    ) {
    }

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
