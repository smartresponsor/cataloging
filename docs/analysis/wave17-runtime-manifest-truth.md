# Wave 17 — runtime manifest truth

## Goal
Tighten the near-RC runtime contour by proving that the key runtime entrypoints are not only present as isolated classes, but can be emitted as a coherent manifest.

## What changed
- Added `category:runtime:manifest` command.
- The command reports the existence of key runtime routes, commands, and contract files.
- Strengthened contract/regression tests to validate runtime markers inside the OpenAPI and route artifacts.

## Practical effect
This wave adds a lightweight but useful runtime manifest seam:
- public read path markers
- admin mutation path markers
- command/runtime entrypoint markers
- contract artifact markers

## Updated status
The component is now closer to an RC gate because runtime surface evidence is available through an explicit manifest command, not only through scattered tests and files.
