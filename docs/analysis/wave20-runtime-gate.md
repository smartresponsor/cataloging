# Wave 20 — runtime gate

## Goal
Tighten the final RC contour by adding a single runtime gate entrypoint that validates manifest/probe/closure linkage against persisted public state.

## Added
- `category:runtime:gate` command
- manifest awareness of the gate command
- probe marker for the gate command
- contract/regression updates

## Result
The runtime contour now has an explicit gate command that combines:
- persisted state load
- public read truth
- route/controller closure markers
- manifest/probe/closure linkage
