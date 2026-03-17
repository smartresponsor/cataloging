# Wave 8 — Admin/controller truth and compatibility-layer tightening

## Scope
This wave targets controller-level truth around admin flows rather than adding more placeholders.

## What changed
- `CategoryMoveController` now validates required identifiers and move policy before delegating.
- `CategoryBulkController` now validates `ids` and `action`, and returns a canonical envelope with counts.
- `CategoryAdminApiController` was tightened into a thin compatibility layer that delegates bulk work to `BulkOperator` and returns stable admin payload shapes.

## Why it matters
Previous waves improved business-flow and operational seams, but the controller boundary was still partially decorative.
This wave makes the admin/controller surface more truthful:
- invalid input is rejected explicitly
- admin bulk path returns deterministic counters
- admin API list path returns stable envelope fields needed by clients

## Effect on scoring
This wave strengthens boundary truth and controller predictability. It does not make the component full production RC yet, but it reduces another class of "green but not proven" risk.
