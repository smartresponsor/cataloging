# Wave 24 — Runtime release-envelope closure

This wave adds a final machine-readable handoff layer above the RC-verdict surface.

## Added
- `category:runtime:release-envelope` command

## Tightened links
- manifest knows release-envelope
- probe knows release-envelope
- gate knows release-envelope
- self-check knows release-envelope
- release-report knows release-envelope
- rc-verdict knows release-envelope
- contract and regression tests know release-envelope

## Result
The runtime contour now ends with an explicit handoff-oriented release envelope over the RC verdict.
