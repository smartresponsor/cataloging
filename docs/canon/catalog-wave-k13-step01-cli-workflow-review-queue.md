# Catalog wave K13 step01 — CLI workflow / review queue parity

## Scope

Add the first dedicated operator-grade CLI commands for the K11 workflow / moderation foundation:
- workflow transition command;
- reviewer queue inspection command.

## Canon notes

- no new root trees;
- no `Port` / `Adaptor` / `Hexagonal` structures;
- commands live under canonical `src/Command`;
- command tests live under `tests/Command`;
- commands depend on existing Symfony-oriented service interfaces.

## Why this step matters

The component already contains workflow and moderation backend capabilities, but the operator surface was still thin. This step starts closing that gap without introducing a new orchestration framework.
