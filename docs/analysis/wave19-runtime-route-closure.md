# Wave 19 — runtime route/controller closure

This wave tightens the runtime contour around the admin move route and the command/runtime surface.

## Changes
- `CategoryMoveController` now exposes `__invoke()` as a thin compatibility entrypoint over `move()`.
- `config/routes/category-move.yaml` is aligned to `CategoryMoveController::__invoke`.
- Added `category:runtime:closure` command to verify persisted public truth together with route/controller/runtime markers.
- Runtime manifest and probe commands now include the new closure command and stronger move-route markers.

## Result
The runtime contour is less synthetic: route YAML, controller entrypoint, persisted state, and runtime commands are now stitched together by direct proof.
