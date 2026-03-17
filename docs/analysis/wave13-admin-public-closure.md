# Wave 13 — admin/public flow closure

## What changed

- `CategoryAdminApiController::list()` is now repository-backed instead of returning a decorative fixed array.
- Admin list intentionally includes draft rows, which makes the admin/public boundary behaviorally explicit.
- `CategoryAdminApiController::bulk()` now validates actions against an explicit whitelist and can delegate publish/unpublish to `CategoryMutationCoordinator`.
- `CategoryMutationCoordinator` closes the gap between bulk mutation and delivery proof by driving repository mutation plus optional delivery pipeline fan-out.

## Why it matters

Previous waves proved individual controller and mutation seams. W13 tightens the cross-surface truth:

- admin read sees drafts
- public read hides drafts by default
- admin bulk publish changes repository state
- the same change becomes visible on the public read path
- delivery proof is produced for the bulk publish path

This is closer to an RC gate because it proves that an admin-side mutation changes the public-side contract rather than only returning a mutation envelope.
