# K12 Step12 hotfix04 — applicability exact-vs-shared reconciliation

## Purpose
Fix strict destination media readiness so shared/global bindings no longer count as exact channel/locale matches.

## Change
`CategoryMediaApplicabilityPolicy` now distinguishes:
- scoped match (shared fallback may satisfy channel/locale scope)
- exact match (binding must explicitly declare target channel and locale)

## Result
- strict readiness remains `false` for fallback-only scenarios
- fallback-aware layers can still elevate publishability where policy allows it
