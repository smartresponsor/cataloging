# Wave 4 golden flow

## Added release-gate behavior

- Introduced `CategoryReleaseWorkflow` to exercise a connected application flow:
  - create
  - move
  - publish
  - outbox dispatch
  - projection runner reset
- Upgraded `CategoryRepository` from a stateless placeholder to a deterministic in-memory truth seam for:
  - create
  - move
  - tree
  - bySlug
  - breadcrumb
  - fullSlug
- Tightened publish request validation to require a boolean flag.

## Why this matters

Previous waves proved isolated seams. This wave proves that a category can move through a realistic lifecycle and leave observable artifacts: repository state, outbox messages, and projection lag reset.

## Interim verdict

The component is still not a true release candidate, but it now has a materially stronger proof surface around its main business chain. The confidence gain comes less from new features and more from turning placeholders into executable truth.
