# Category review decision coupling backend

K11 Step05 introduces a narrow backend orchestration layer that couples accepted or rejected moderation decisions to category workflow state and publication readiness evaluation.

## Purpose

This step keeps moderation, workflow, and publish governance aligned without rewriting the legacy publish stack.

## Scope

- accepted review decisions move category workflow to `approved`
- accepted review decisions trigger publication readiness evaluation
- rejected review decisions move category workflow back to `draft`
- rejected review decisions explicitly remain non-publishable

## Contracts

- `CategoryReviewDecisionCouplingService`
- `CategoryReviewDecisionCouplingResult`
- `CategoryReviewDecisionCoupled`

## Guarantees

- no generic workflow engine
- no Port / Adaptor / Hexagonal skeleton
- no forced rewrite of existing publish endpoints
- Symfony-oriented layer placement only

## Follow-up

This coupling layer is intended to support richer completeness and readiness modeling in the next wave.
