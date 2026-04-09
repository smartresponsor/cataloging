# Category Media Governance Backend

This wave introduces a governed backend foundation for category media bindings.

## What is covered

- explicit media roles:
  - `primary`
  - `banner`
  - `icon`
  - `thumbnail`
  - `hero`
- category-to-asset bindings
- channel-specific applicability
- locale-specific applicability
- publish-critical media flags
- binding audit events

## Why this matters

The component already had attachment and banner traces, but they were not shaped as a single governed backend capability.
This wave creates a cleaner service-layer foundation that later waves can use for:

- media completeness requirements
- destination-specific media packaging
- richer publish blockers for channels/locales
- external media validation and versioning

## Current limits

This is intentionally a backend-only foundation.
It does **not** yet implement:

- asset storage
- renditions
- DAM synchronization
- media approval workflow
- media version lineage

Those can be layered on top without changing the current Symfony-oriented canonical layout.
