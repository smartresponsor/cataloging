# Wave 16 — persisted runtime read truth

## What changed
- Added `category:tree:dump` as a runtime read entrypoint.
- The command can load repository state from a file-backed adapter and emit a canonical public-tree payload.
- Added tests proving `publish -> save -> next-process dump` closure.

## Why it matters
This wave closes another RC-adjacent gap: state is not only persisted after mutation, it is also consumed by a separate runtime path that reconstructs public read truth from that persisted state.

## Net effect
The runtime contour is more adult: one process mutates and saves, another process reads and serves the same truth.
