# Cataloging wave 2 — RC backbone

## Scope of this wave

This wave does not expand the feature surface. It raises proof density around the existing category core.

## What was strengthened

- category voter now supports publish authorization on current Symfony voter contract
- category query accepts nullable ResolveInfo so direct resolver tests stop fighting framework plumbing
- category repository now satisfies the declared interface contract fully
- placeholder tests were replaced with behavior-oriented tests across:
  - category cache
  - category voter
  - tree invariants
  - publish flow seam
  - GraphQL filtering
  - audit logging
  - webhook publisher contract
  - repository return shapes
  - publish request validation

## Remaining gaps

- no true HTTP kernel-level contract tests yet
- no database-backed repository truth tests yet
- no outbox/projection synchronization proof yet
- no full create + persist + move + publish + invalidate + emit integration flow yet

## Interim verdict

This wave improves confidence materially, but the component remains pre-RC until kernel/infrastructure truth tests exist.
