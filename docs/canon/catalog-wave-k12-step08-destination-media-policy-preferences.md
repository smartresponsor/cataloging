# Catalog wave K12 Step08 — destination media policy preferences

## Scope

Introduce destination-level policy preferences for media coverage resolution without adding a new architectural tree.

## Added capabilities

- strict exact media policy mode
- fallback-allowed media policy mode
- prefer-exact-but-warn mode
- normalized evaluation payload for downstream gating

## Canon status

- single Symfony-oriented application preserved
- `App\ -> src/` preserved
- no `Port` / `Adaptor` / hexagonal scaffolding introduced
- additions stay within canonical layer roots
