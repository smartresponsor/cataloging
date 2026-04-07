# Category boundary policy

Cataloging is a **catalog domain brick**.

It owns:
- category taxonomy and tree management;
- category workflow, publication, review, governance, and syndication rules;
- catalog write pipeline, outbox, projection, and read/search models;
- tenant-aware and resource-aware authorization for catalog actions;
- external identity claim mapping into local catalog policy;
- category-to-attachment binding to external attachment systems;
- producer-side documentation, OpenAPI, and readiness evidence.

It does **not** own:
- authentication source of truth;
- user/password/session platform concerns;
- binary/media storage or file processing;
- checkout, order, pricing, payment, or inventory domains;
- central documentation site assembly or Antora playbook orchestration;
- central observability platform ownership.

## Boundary rule

Cataloging may integrate with external bricks, but it must not absorb them.

- Auth remains external; Cataloging validates trusted tokens/claims and applies local authorization.
- Attachments remain external; Cataloging stores category bindings and validates binding contracts.
- Documentation remains producer-only; central aggregation belongs outside this repository.
