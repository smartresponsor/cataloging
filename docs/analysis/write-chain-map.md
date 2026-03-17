# Cataloging write-chain map (interim)

## Scope
Interim mapping based on current repository snapshot. This is a chain inventory, not a proof of runtime completeness.

## Primary write chain A — category create/move/attach/detach
- Entry point: `App\Controller\CategoryController`
- Create method: `create(array $body, array $route, array $auth)`
- Move method: `move(array $body, array $route, array $auth)`
- Service: `App\Service\CatalogCategory`
- Policy gate: `App\PolicyInterface\CategoryPolicyInterface`
- Persistence: `App\RepositoryInterface\CategoryRepositoryInterface`
- Side effects: `Psr\EventDispatcher\EventDispatcherInterface`
- Emitted events observed in service:
  - `App\Event\CategoryCreated`
  - `App\Event\CategoryMoved`
  - `App\Event\CategoryLinked`
  - `App\Event\CategoryUnlinked`

### Chain status
- Exists as a concrete service-driven application chain.
- `move()` emits an event but currently lacks a visible explicit invariant check in this service itself.
- Publish is not implemented here.

## Primary write chain B — admin move/rebase flow
- Entry point: `App\Controller\Admin\CategoryMoveController`
- Route files:
  - `config/routes/category-move.yaml`
  - `config/config/routes/category-move.yaml`
- Service contract: `App\Service\CatalogCategoryMoveInterface`
- Service implementation: `App\Service\CatalogCategoryMoveService`
- Declared semantics: transactional rebase of node path under a new parent.
- Output tuple: `[changedCount, redirects]`

### Chain status
- Exists as a dedicated move/rebase seam.
- Current implementation is skeletal: transaction opens/closes, but change accounting and redirect generation are placeholders.
- This is a high-risk chain that needs truth tests before RC.

## Primary write chain C — API move/publish endpoints
- Entry point: `App\Controller\CategoryApiController`
- Move DTO: `App\Request\MoveCategoryRequest`
- Publish DTO: `App\Request\PublishCategoryRequest`
- Current behavior: validates request payload shape and returns `status=ok`.

### Chain status
- Transport chain exists.
- Application linkage is weak/incomplete: controller currently validates and returns success without visible mutation service integration.
- Publish should not be considered proven.

## Primary write chain D — bulk operations
- Entry point candidate: `App\Service\CatalogCategoryBulk`
- Supported operation names observed:
  - `create`
  - move-like batch actions (needs deeper implementation proof)
- Delegation target: category service

### Chain status
- Batch seam exists.
- Needs dedicated chain proof and tests.

## Event/outbox/projection side-effect seams
- Outbox writer: `App\Service\OutboxWriter`
- Projection consumer: `App\Service\ProjectionWorker`
- Projection sync variants also exist under:
  - `App\Projection\CategoryProjectionSync`
  - `App\Projection\CategoryProjectionRunner`
  - `App\Infrastructure\ProjectionSync`

### Chain status
- Side-effect infrastructure exists in multiple variants.
- Boundary between production path and placeholder path is not yet fully compressed.
- This area should be treated as partially mapped, not validated.

## Webhook side effects
- Publishers:
  - `App\Infrastructure\ProductWebhookPublisher`
  - `App\Infrastructure\OrderWebhookPublisher`
- Generic notifier seam: `App\Service\WebhookNotifier`

### Chain status
- Publisher seam exists.
- Current visible publisher behavior is a simple HTTP POST.
- Retry/error semantics are not proven in current test set.

## High-risk gaps from write mapping
1. Publish path is transport-visible but not strongly linked to application mutation logic.
2. Admin move chain exists but implementation remains placeholder-heavy.
3. Outbox/projection path exists in multiple forms; canonical runtime path is not yet fully obvious.
4. Webhook path exists but contract/failure semantics are lightly proven.
