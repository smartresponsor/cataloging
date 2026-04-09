# Category review queue backend

## Purpose

This backend layer adds reviewer assignment and review queue visibility on top of change requests.

## Scope

- assign a reviewer to a category change request
- keep assignment priority and optional due date
- expose a reviewer-specific queue
- compute review readiness without coupling to UI

## Readiness model

A queue item is considered ready for review when:

- the request is already in `in_review`
- the request summary is not empty
- the request contains a non-empty change payload

Warnings are emitted for incomplete review preparation, for example:

- `request_not_started`
- `request_summary_missing`
- `request_changes_missing`

## Notes

This wave intentionally keeps storage lightweight and in-memory.
A later wave can project the queue into persistent read models without changing the current backend contracts.
