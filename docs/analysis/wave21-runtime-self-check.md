# Wave 21 — Runtime Self-Check Closure

This wave adds a machine-readable runtime self-check entrypoint so the runtime contour can be verified from one command and referenced by manifest/probe/gate layers.

Key additions:
- `category:runtime:self-check`
- manifest references the self-check command
- probe and gate layers now validate self-check presence
- contract/regression tests include the self-check runtime artifact
