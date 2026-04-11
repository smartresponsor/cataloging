<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

/**
 * Carries payload surfaces used to compose a governance trail report.
 */
final readonly class CategorySyndicationGovernanceTrailPayloadSet
{
    /**
     * @param array<string,mixed> $policyAwarePayload
     * @param array<string,mixed> $deliveryPayload
     * @param array<string,mixed> $historyPayload
     * @param array<string,mixed> $recoveryPayload
     */
    public function __construct(
        private array $policyAwarePayload,
        private array $deliveryPayload,
        private array $historyPayload,
        private array $recoveryPayload,
    ) {
    }

    /** @return array<string,mixed> */
    public function policyAwarePayload(): array
    {
        return $this->policyAwarePayload;
    }

    /** @return array<string,mixed> */
    public function deliveryPayload(): array
    {
        return $this->deliveryPayload;
    }

    /** @return array<string,mixed> */
    public function historyPayload(): array
    {
        return $this->historyPayload;
    }

    /** @return array<string,mixed> */
    public function recoveryPayload(): array
    {
        return $this->recoveryPayload;
    }
}
