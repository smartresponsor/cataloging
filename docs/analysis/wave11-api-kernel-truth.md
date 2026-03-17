# Wave 11 — API/kernel-adjacent truth

## Focus
- strengthen `CategoryApiController` beyond stub-level envelopes
- replace placeholder API/auth tests with config- and contract-truth checks
- tighten collection endpoint envelope semantics

## Changes
- `CategoryApiController`
  - canonical envelopes for tree/move/publish
  - deterministic seeded repository fallback
  - locale/taxonomy/depth semantics on tree read
  - canonical mutation envelopes for move/publish
- `CategoryCollectionController`
  - canonical envelope with `ok`, `count`, `rulesCount`, `locale`
  - locale-aware filtering of built collection rows
- tests
  - stronger controller truth for category API
  - OpenAPI contract presence checks
  - security/rbac config truth checks
  - collection controller truth test

## Effect
This wave improves API truth without pretending to have full framework-kernel integration available in the snapshot. It raises confidence that the public controller/config surface is aligned and less decorative.
