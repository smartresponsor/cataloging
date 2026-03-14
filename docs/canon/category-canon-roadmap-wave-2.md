# Category canon roadmap wave 2

Current milestone: canon-stable-rc / phase-2 structural cleanup.

Goals:
- keep current runtime surface intact
- make non-canonical trees explicit and machine-detectable
- prepare controlled removal of shadow config and forbidden source roots
- continue cleanup in flat patch waves

Canonical rules targeted by this wave:
- default App namespace
- no src/Domain root
- no Port/Adapter pattern
- Infrastructure spelling only; no Infra short form
- singular naming
- component-bound classes should converge toward Category* prefix
- duplicate config/config tree must be removed after owner confirmation

Next waves:
1. shadow-config deprecate markers
2. suspicious file tombstones
3. forbidden-root evacuation plan
4. namespace/path consolidation
5. duplicate-owner merge by capability
