# Category API documentation strategy

## Current state

The repository already ships a static OpenAPI contract draft:

- `api/category-openapi.yaml`

This is the lowest-risk source of truth for RC packaging because it is explicit, reviewable and does not depend on runtime route introspection.

## Recommendation for this RC line

Use a **static OpenAPI-first** approach for the RC package:

1. keep `api/category-openapi.yaml` as the contract draft
2. expose it through Swagger UI or ReDoc for human readability
3. keep route inventory / runtime proof reports as engineering validation
4. postpone deeper runtime-driven documentation generation until the API surface is intentionally frozen

## Why not jump straight to full API Platform adoption

This repository contains controller- and route-oriented runtime code and an explicit OpenAPI file.
A full API Platform move would only be worth it if the component intentionally chooses API Platform as a primary runtime/documentation model.

At the moment, this repository is better served by:
- explicit OpenAPI file
- Swagger UI / ReDoc rendering
- optional route-based OpenAPI generation as a supporting tool, not the primary source of truth

## Practical RC options

### Option A — preferred for this RC
- keep `api/category-openapi.yaml`
- add Swagger UI or ReDoc rendering in docs/public tooling
- treat the YAML file as the human-reviewable contract

### Option B — route-based supplementary documentation
- add `NelmioApiDocBundle`
- generate OpenAPI from Symfony routes and attributes
- keep the static YAML as the reviewed contract, and use generated docs as secondary support

### Option C — future, only if deliberately adopted
- use API Platform as a primary runtime/documentation layer
- only do this when the component explicitly decides to model the API around API Platform resources and operations

## Current repository note

The repository currently contains `config/packages/api_platform.yaml`, but the current `composer.json` line does not include `api-platform/core`.
For RC packaging, treat the static OpenAPI YAML as the real contract and the API Platform config as non-authoritative until the dependency choice is made explicitly.

## ReDoc note

ReDoc can live in parallel with Swagger UI over the same OpenAPI source without changing the contract model of the repository.
