# Category syndication history backend

K11 step12 adds destination-specific publish history and recovery/audit consolidation for category syndication.

## Added backend capabilities

- destination-specific history rollup from delivery records
- status distribution per destination
- latest delivered timestamp per destination
- max attempt visibility across destination history
- recovery audit summary for failed, retryable, non-retryable, scheduled-retry, and delivered-after-retry flows

## Main backend contracts

- `App\ValueObject\CategorySyndicationDestinationHistory`
- `App\ValueObject\CategorySyndicationRecoveryAuditSummary`
- `App\Policy\CategorySyndicationHistoryPolicy`
- `App\Service\CategorySyndicationHistoryService`
- `App\Event\CategorySyndicationDestinationHistoryBuilt`
- `App\Event\CategorySyndicationRecoveryAuditConsolidated`

## Why this matters

This wave closes an important backend gap versus more mature commerce/PXM stacks: the component can now consolidate downstream delivery evidence per destination, not only emit per-attempt delivery and retry events.

That makes later destination dashboards, reporting APIs, and operational audit surfaces easier to add without reworking the existing Symfony-oriented backend structure.
