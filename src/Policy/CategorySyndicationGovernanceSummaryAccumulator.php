<?php

declare(strict_types=1);

namespace App\Policy;

/**
 * Accumulates governance trail counters shared by governance summary policies.
 */
final class CategorySyndicationGovernanceSummaryAccumulator
{
    /** @var array<string,int> */
    private array $statusCounts = [
        'pending' => 0,
        'delivered' => 0,
        'failed' => 0,
        'retry_scheduled' => 0,
        'skipped' => 0,
    ];

    /** @var array<string,int> */
    private array $policyModeCounts = [
        'strict_exact' => 0,
        'allow_fallback' => 0,
        'prefer_exact_warn' => 0,
    ];

    /** @var list<string> */
    private array $warningCodes = [];

    private int $resolvedPublishableCount = 0;
    private int $fallbackUsedCount = 0;
    private int $retryableCount = 0;
    private int $retryScheduledCount = 0;
    private int $failureTrailCount = 0;
    private int $deliveredTrailCount = 0;
    private int $totalTrails = 0;

    /** @param list<array<string,mixed>> $trailPayloads */
    public static function fromPayloads(array $trailPayloads): self
    {
        $accumulator = new self();
        foreach ($trailPayloads as $payload) {
            $accumulator->ingest($payload);
        }

        return $accumulator;
    }

    /** @param array<string,mixed> $payload */
    public function ingest(array $payload): void
    {
        ++$this->totalTrails;

        $status = self::scalarString($payload['deliveryStatus'] ?? 'pending');
        if ('' !== $status) {
            $this->statusCounts[$status] = ($this->statusCounts[$status] ?? 0) + 1;
        }

        $mode = self::scalarString($payload['mediaPolicyMode'] ?? 'strict_exact');
        if ('' !== $mode) {
            $this->policyModeCounts[$mode] = ($this->policyModeCounts[$mode] ?? 0) + 1;
        }

        if ($payload['resolvedPublishable'] ?? false) {
            ++$this->resolvedPublishableCount;
        }
        if ($payload['fallbackUsed'] ?? false) {
            ++$this->fallbackUsedCount;
        }
        if ($payload['retryable'] ?? false) {
            ++$this->retryableCount;
        }
        if ($payload['retryScheduled'] ?? false) {
            ++$this->retryScheduledCount;
        }

        $checks = is_array($payload['checks'] ?? null) ? $payload['checks'] : [];
        if ($checks['governanceTrailHasFailures'] ?? false) {
            ++$this->failureTrailCount;
        }
        if ($checks['governanceTrailHasDelivered'] ?? false) {
            ++$this->deliveredTrailCount;
        }

        foreach (self::stringList($payload['warnings'] ?? null) as $warning) {
            if (!in_array($warning, $this->warningCodes, true)) {
                $this->warningCodes[] = $warning;
            }
        }
    }

    public function totalTrails(): int
    {
        return $this->totalTrails;
    }

    public function resolvedPublishableCount(): int
    {
        return $this->resolvedPublishableCount;
    }

    public function fallbackUsedCount(): int
    {
        return $this->fallbackUsedCount;
    }

    public function retryableCount(): int
    {
        return $this->retryableCount;
    }

    public function retryScheduledCount(): int
    {
        return $this->retryScheduledCount;
    }

    public function failureTrailCount(): int
    {
        return $this->failureTrailCount;
    }

    public function deliveredTrailCount(): int
    {
        return $this->deliveredTrailCount;
    }

    /** @return array<string,int> */
    public function statusCounts(): array
    {
        return $this->statusCounts;
    }

    /** @return array<string,int> */
    public function policyModeCounts(): array
    {
        return $this->policyModeCounts;
    }

    /** @return list<string> */
    public function warningCodes(): array
    {
        $warningCodes = $this->warningCodes;
        sort($warningCodes);

        return $warningCodes;
    }

    private static function scalarString(mixed $value): string
    {
        return is_scalar($value) ? trim((string) $value) : '';
    }

    /** @return list<string> */
    private static function stringList(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $result = [];
        foreach ($value as $item) {
            if (!is_scalar($item)) {
                continue;
            }
            $normalized = trim((string) $item);
            if ('' !== $normalized) {
                $result[] = $normalized;
            }
        }

        return array_values($result);
    }
}
