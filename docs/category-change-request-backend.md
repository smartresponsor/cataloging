# Category Change Request Backend

## Purpose

This backend capability adds a governed **change-request and moderation contour** for category operations.

It is intentionally narrow:

- submit a category change request,
- keep the request in an explicit moderation state,
- allow a reviewer to accept, reject, withdraw, or move it into review,
- preserve review history as event payloads.

## Scope of this wave

Introduced in K11 Step03:

- `CategoryChangeRequestState`
- `CategoryChangeRequest`
- `CategoryChangeRequestPolicy`
- `CategoryChangeRequestRepository`
- `CategoryChangeRequestService`
- `CategoryChangeRequestReviewed`

## Business intent

This wave prepares the backend for market-default collaboration expectations:

- structured moderation,
- review accountability,
- decision reasons,
- non-UI operator workflows.

It does **not** yet implement:

- task inboxes,
- SLA tracking,
- notification routing,
- persistence integration,
- full approval orchestration with publication coupling.

Those remain follow-up waves.
