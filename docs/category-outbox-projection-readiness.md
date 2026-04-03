# Category Outbox / Projection Readiness

## Purpose
This check validates that category write flows do not stop at audit/outbox creation and that the repository contains a concrete projection processing baseline.

## Current baseline
- outbox rows now carry `available_at`, `attempts`, `last_error`, `dispatched_at`, and `dead_lettered_at`
- projection processing is DBAL-based rather than comment-only
- failed projection application is retried with bounded exponential backoff
- repeated failures are moved into a dead-letter state
- `app:category:projection:run` exists for replay / projection warmup
- the canonical infra projection schema includes publication and path fields needed by read-side APIs

## Remaining expectations for GA
- run the projection command against a real `infra` MySQL connection
- expose queue depth / dead-letter counts / projection lag to real exporters
- add replay and poison-message drills to CI or release operations
