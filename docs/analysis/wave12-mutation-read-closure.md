# Wave 12 — Mutation/Read Closure

This wave tightened the mutation-backed read truth in three places:

1. `CategoryRepository` now has deterministic publish-state mutation helpers.
2. `BulkOperator` can use a repository-backed publish/unpublish seam for string ids.
3. `CategoryApiController::publish()` now mutates repository state and, when injected, emits a delivery flow proof through outbox/projection/webhook.

## Main behavior gains

- publish is no longer a response-only stub
- bulk publish/unpublish can affect the same repository instance that powers read-side assertions
- end-to-end publish -> read and bulk -> read truth is now testable without booting the full kernel

## Remaining gaps

- database adapter realism is still synthetic/in-memory
- controller-to-kernel truth is stronger, but not yet fully framework-booted
- projection/webhook chain is still contract-level rather than environment-level
