# Wave 7 — Import/Export truth and command backbone

## Scope
- Replace placeholder import/export commands with delegating application commands.
- Make NDJSON exporter emit deterministic category rows from repository tree.
- Make NDJSON importer exercise category/link semantics and explicit dry-run behavior.
- Add tests for importer, exporter, and both console commands.

## Outcome
This wave upgrades import/export from placeholder shell behavior to a testable application seam.
It still uses in-memory/repository truth rather than a real adapter-backed transport, so the component remains pre-RC rather than full production RC.

## Net effect
- Stronger operator confidence for CLI paths.
- Better truth coverage for NDJSON flows.
- Cleaner handoff from command layer to importer/exporter services.
