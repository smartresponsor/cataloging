# Wave 23 — RC Verdict Closure

## Focus

Add a machine-readable RC verdict layer on top of the existing runtime release-report surface.

## What changed

- Added `category:runtime:rc-verdict` command.
- Wired manifest/probe/gate/self-check/release-report to know about the RC verdict layer.
- Extended contract/regression coverage to reference the new command surface.

## Result

The runtime contour now ends with an explicit RC verdict summary instead of stopping at a generic release report.
