# Category RC declaration

owner: Marketing America Corp  
component: category  
status: rc-candidate  
reviewed_at: 2026-03-13  
contract_version: 1.0.0-rc1  
release_line: uncut-rc-candidate

## What we have

- canonical structure after consolidation waves W1–W6
- local proof-ready composer/test/smoke/report contour
- OpenAPI contract draft in `api/catalog-openapi.yaml`
- GraphQL surface present
- runtime/read/write/projection/webhook paths present
- fixture/demo/parity layer present

## What we are declaring

This repository is suitable to be treated as the first real release-candidate candidate of the Catalog component.

This means:
- the repository is readable and navigable
- the runtime contour is coherent
- proof commands exist
- the main component boundary is documented
- the remaining work is packaging polish and final release discipline, not core structural rescue

## What we are not declaring

This declaration does **not** claim:
- GA
- permanent API freeze
- permanent schema freeze
- final deployment certification
- final compatibility promises for every downstream consumer
