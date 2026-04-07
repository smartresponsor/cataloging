# Category operational proof boundary

Cataloging may publish readiness and inspection artifacts, but it must not drift into a self-hosted governance platform.

## Boundary rule

- machine-readable readiness/inspection logic belongs under `tools/inspection/`;
- Antora and narrative docs may reference those artifacts;
- runtime application code must not depend on `report/inspection/` outputs to function;
- central aggregation, dashboards, and global governance orchestration remain external.

## Producer-only stance

This repository is a producer of:
- narrative docs;
- generated OpenAPI and public docs artifacts;
- machine-readable readiness evidence.

It is not responsible for central site assembly or platform-level evidence aggregation.
