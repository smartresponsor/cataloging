# Wave 22 — Runtime Release Report Closure

## What changed
- Added `category:runtime:release-report` as a machine-readable RC summary entrypoint.
- Wired manifest/probe/gate/self-check layers to the release-report command.
- Extended regression and contract checks to include the new runtime command.

## Why it matters
This wave tightens the final runtime contour by adding a single command that summarizes runtime readiness after process-boundary state load. It reduces the remaining synthetic gap between self-check mechanics and RC-facing reporting.

## Result
The runtime surface now closes through:
- manifest
- probe
- gate
- self-check
- release-report
