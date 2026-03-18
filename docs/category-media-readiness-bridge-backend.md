# Category media readiness bridge backend

## Goal

Connect governed category media bindings to completeness evaluation and publication quality without rewriting the legacy publish or attachment flows.

## What this step adds

- governed media coverage evaluation for category bindings
- completeness bridge that merges media coverage checks into category completeness
- publication quality bridge that promotes missing required governed media coverage into a hard blocker

## Governed media coverage checks

- `mediaReady`
- `bannerReady`
- `heroReady`
- `requiredMediaCoverageReady`

## Quality effect

`requiredMediaCoverageReady = false` is treated as a hard blocker in publication quality.

## Architectural note

This remains Symfony-oriented and additive:

- existing completeness service is not replaced
- existing publication quality service is not replaced
- legacy attachment and publish flows stay intact
