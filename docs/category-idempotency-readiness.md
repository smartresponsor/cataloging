# Category Idempotency Readiness

## Purpose
This check validates that category mutation commands no longer rely on process-local memory for duplicate suppression and that the repository contains a durable shared idempotency baseline.

## Current baseline
- mutation flows use a DB-backed `category_idempotency` table instead of process-local state
- idempotency keys carry operation and request-hash semantics so key reuse with a different payload is rejected
- mutation endpoints accept `X-Idempotency-Key` and `X-Correlation-ID` headers
- duplicate move/publish commands return duplicate-aware no-op responses instead of writing a second audit/outbox event
- a purge command exists for expired idempotency rows

## Remaining expectations for GA
- expose duplicate-command metrics to real exporters
- consider Redis-backed implementation if idempotency hot-path contention appears in production
- add end-to-end duplicate-request contract tests in a fully provisioned CI environment
