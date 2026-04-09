# Category Syndication Retry Backend

K11 step11 adds a narrow retry and failed-delivery recovery foundation for category syndication.

## Scope

- failed-delivery recovery candidate preparation
- retryable classification
- normalized retry scheduling payload
- bounded retry delay schedule

## Retryable classification

Retry is allowed for:

- missing response code
- HTTP 429
- HTTP 5xx

## Delay schedule

- attempt 1 -> 300 seconds
- attempt 2 -> 900 seconds
- attempt 3 -> 1800 seconds
- attempt 4 -> 3600 seconds

## Non-goals

This step does not introduce a new orchestration framework and does not rewrite the existing publication flow.
