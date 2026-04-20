# Category workflow backend foundation

This document describes the **K11 Step01** workflow foundation added on top of the K10 clean slice.

## Purpose

The goal is to make category lifecycle more explicit than a simple draft/publish toggle.

The foundation introduces:

- explicit workflow states,
- guarded transition policy,
- transition reason requirement,
- actor attribution,
- in-memory repository contract for current state and transition history,
- auditable transition event payload.

## Initial state model

The first workflow state set is:

- `draft`
- `in_review`
- `approved`
- `published`
- `archived`

## Initial transition matrix

- `draft -> in_review | archived`
- `in_review -> draft | approved | archived`
- `approved -> draft | published | archived`
- `published -> draft | archived`
- `archived -> draft`

## Contracts introduced

- `App\Cataloging\ValueObject\CategoryWorkflowState`
- `App\Cataloging\Entity\CategoryWorkflow`
- `App\Cataloging\Policy\CategoryWorkflowPolicy`
- `App\Cataloging\Repository\CategoryWorkflowRepository`
- `App\Cataloging\Service\CategoryWorkflowTransitionService`
- `App\Cataloging\Event\CategoryWorkflowTransitioned`

## Why the repository is intentionally simple

K11 Step01 is a backend foundation wave, not a persistence rewrite.

The repository is intentionally narrow and in-memory so the workflow model can be introduced without disturbing the now-green K10 runtime base.
A later wave can project or persist the same contracts more deeply if the component needs durable storage in production.

## Why this is market-relevant

This closes part of the maturity gap between a clean category backend and a more enterprise-ready taxonomy backend:

- transitions are now explicit,
- moderation intent exists,
- audit signals exist,
- later approval gates can reuse this model.

## Next planned steps

The immediate follow-up waves should add:

- publication gates,
- completeness checks,
- deeper workflow history retrieval and hardening.
