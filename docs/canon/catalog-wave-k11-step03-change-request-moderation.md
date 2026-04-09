# Catalog wave K11 Step03 — change-request moderation foundation

## Objective

Add a backend-only change-request and moderation foundation without disturbing the stabilized Symfony-oriented component layout.

## Added capability

- explicit change-request state value object,
- submitted request entity,
- submit and review policy,
- lightweight repository contract,
- review event payload,
- service layer for submission and moderation.

## Architectural notes

The wave stays canonical because it:

- keeps `App\ -> src/`,
- extends existing layer roots only,
- uses `src/[Layer]` and `src/[Layer]Interface`,
- does not introduce `Port`, `Adaptor`, `Infra`, or parallel app trees,
- does not widen into a generic workflow framework.

## Follow-up value

This foundation enables later waves for:

- approval gating,
- reviewer assignment,
- proposal provenance,
- audit-grade moderation history,
- coupling to publication readiness.
