# Wave 14 — command/controller closure

## What changed

- Added `category:publish` console command as a thin Symfony application entrypoint over the repository-backed mutation coordinator.
- Tightened `CategoryAdminApiController::bulk()` to return public-read proof after publish/unpublish mutations.
- Added command/controller truth tests to prove both CLI and admin API close the loop into public read state.

## Why this matters

The component is closer to an RC gate when mutations are not only visible through HTTP controllers but also through a real command entrypoint. This wave reduces the gap between console operations, admin mutations, delivery proof, and public read truth.

## Proof seams strengthened

- admin bulk publish now exposes `publicCountAfter` and `publicIdsAfter`
- admin bulk unpublish proves the inverse closure
- `category:publish` command proves CLI -> mutation -> delivery -> public-read closure

## Residual gap

Persistence is still primarily synthetic/in-memory rather than adapter-backed, so the component remains a strong internal RC candidate rather than a fully production-proven RC.
