# Cataloging chain-to-test matrix (interim)

| Chain | Status | Evidence | Gap |
|---|---|---|---|
| Create category | Partial | Structural mapping only | No truth test for controller->service->repo->event |
| Move category (service chain) | Partial | Tree operation seam test | No end-to-end mutation proof |
| Admin move/rebase | Weak | Controller and service seam visible | Implementation placeholder, no truth test |
| Publish | Weak | Voter seam + publish DTO exist | No convincing publish flow test |
| Tree read | Partial | Controller/repository seam visible | No controller truth test |
| GraphQL read | Partial | Locale filter seam test | Runtime authority and schema flow weak |
| Tenant filter | Moderate | Direct unit test | Not tied to full merchant/public flows |
| Webhook publish | Weak | Instantiation/smoke level | No payload/headers/failure contract proof |
| Outbox -> projection | Weak | Classes exist | No chain proof |
| Import | Weak | CLI/importer seams exist | No test proof |
| Export | Weak | CLI/export seams exist | No test proof |

## Interim conclusion
The repository demonstrates breadth of capability, but RC confidence is limited by weak proof on the highest-risk chains.
