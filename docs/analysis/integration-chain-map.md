# Cataloging integration-chain map (interim)

## Webhook
- Product publisher: `App\Infrastructure\ProductWebhookPublisher`
- Order publisher: `App\Infrastructure\OrderWebhookPublisher`
- Generic notifier: `App\Service\WebhookNotifier`
- Config hints:
  - `config/packages/category_webhook.yaml`
  - `config/packages/category_webhook_v2.yaml`
  - sample JSON webhook config files

### Current state
- Integration seam exists.
- Contract shape is simple and understandable.
- Runtime retry/error semantics are not yet strongly proven.

## Import
- CLI entry point: `App\Command\ImportCategoryCommand`
- Importer: `App\Importer\CategoryNdjsonImporter`

### Current state
- Import chain exists.
- Needs dedicated truth tests and failure-mode map.

## Export
- CLI entry points:
  - `App\Command\ExportCategoryCommand`
  - `App\Command\ExportRedirectCommand`
  - `App\Command\ExportRedirectNdjsonCommand`

### Current state
- Export surface is broad.
- Test proof is not visible in current narrow test pack.

## Projection / sync
- `App\Service\ProjectionWorker`
- `App\Projection\CategoryProjectionSync`
- `App\Projection\CategoryProjectionRunner`
- `App\Infrastructure\ProjectionSync`

### Current state
- Multiple sync/projection paths exist.
- Canonical runtime path still needs compression and proof.

## Outbox
- `App\Service\OutboxWriter`
- `src/Event/Outbox/*`

### Current state
- Outbox seam exists and points toward a reliable integration architecture.
- End-to-end proof is still missing.
