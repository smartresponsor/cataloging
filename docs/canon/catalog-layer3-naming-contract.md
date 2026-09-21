# Cataloging layer 3 naming contract

This document is literal and normative for the current Cataloging RC line.

## Canonical namespace and layer roots

Production code uses `App\\Cataloging\\`. Test code uses `App\\Cataloging\\Tests\\`.

Canonical layer roots follow AGENTS.md and stay Symfony-oriented:

- `src/Entity/`
- `src/EntityInterface/`
- `src/Controller/`
- `src/ControllerInterface/`
- `src/Event/`
- `src/EventInterface/`
- `src/Repository/`
- `src/RepositoryInterface/`
- `src/Service/`
- `src/ServiceInterface/`
- `src/ValueObject/`
- `src/Exception/`

No new `src/Domain`, Port, Adapter, or Adaptor tree is part of this repository canon.

## Literal class-name contract

- entity class: starts with `Catalog`, ends with `Entity`
- controller class: starts with `Catalog`, ends with `Controller`
- controller interface: starts with `Catalog`, ends with `ControllerInterface`
- event class: starts with `Catalog`, ends with `Event`
- event interface: starts with `Catalog`, ends with `EventInterface`
- repository class: starts with `Catalog`, ends with `Repository`
- repository interface: starts with `Catalog`, ends with `RepositoryInterface`

## Literal namespace contract

- `App\\Cataloging\\Entity`
- `App\\Cataloging\\Controller`
- `App\\Cataloging\\ControllerInterface`
- `App\\Cataloging\\Event`
- `App\\Cataloging\\EventInterface`
- `App\\Cataloging\\Repository`
- `App\\Cataloging\\RepositoryInterface`

## Forbidden examples

- `src/Domain/CatalogCategory.php`
- `src/Port/CatalogCategoryPort.php`
- `src/Adapter/CatalogCategoryAdapter.php`
- wildcard controller scans for generic CRUD routes
- generic CRUD controllers owned by Cataloging

## Allowed examples

- `src/Entity/CatalogCategoryEntity.php`
- `src/Repository/CatalogCategoryRepository.php`
- `src/RepositoryInterface/CatalogCategoryRepositoryInterface.php`
- `src/Controller/CatalogCategoryController.php`
- `src/Event/CatalogCategoryPublishedEvent.php`

## Machine rule

If a generator, agent, or automated refactor proposes a different tree or a different naming shape, the proposal is wrong and must be corrected before merge.
