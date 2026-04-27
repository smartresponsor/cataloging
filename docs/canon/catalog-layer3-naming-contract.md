# Catalog layer 3 structure and naming contract

This document is literal and normative.

## Canonical inner trees

Only the following inner trees are allowed.

- `src/Entity/Catalog/`
- `src/Controller/Catalog/`
- `src/ControllerInterface/Catalog/`
- `src/Event/Catalog/`
- `src/EventInterface/Catalog/`
- `src/Repository/Catalog/`
- `src/RepositoryInterface/Catalog/`

For each listed layer:
- the parent layer folder may contain only the `Catalog/` folder;
- the `Catalog/` folder may not contain subfolders;
- the `Catalog/` folder may contain only classes of that exact layer.

## Literal class-name contract

- entity class: starts with `Catalog`, ends with `Entity`
- controller class: starts with `Catalog`, ends with `Controller`
- controller interface: starts with `Catalog`, ends with `ControllerInterface`
- event class: starts with `Catalog`, ends with `Event`
- event interface: starts with `Catalog`, ends with `EventInterface`
- repository class: starts with `Catalog`, ends with `Repository`
- repository interface: starts with `Catalog`, ends with `RepositoryInterface`

## Literal namespace contract

- `App\\Entity\\Catalog`
- `App\\Controller\\Catalog`
- `App\\ControllerInterface\\Catalog`
- `App\\Event\\Catalog`
- `App\\EventInterface\\Catalog`
- `App\\Repository\\Catalog`
- `App\\RepositoryInterface\\Catalog`

## Forbidden examples

- `src/Entity/CatalogCategoryEntity.php`
- `src/Entity/Role/SubjectId.php`
- `src/Entity/Catalog/Subtree/CatalogCategoryEntity.php`
- `src/Repository/Category/CatalogCategoryRepository.php`
- `src/Event/Catalog/Sub/CatalogCategoryPublishedEvent.php`
- `src/Controller/Catalog/CategoryController.php`
- `src/RepositoryInterface/Catalog/CategoryRepositoryInterface.php`

## Allowed examples

- `src/Entity/Catalog/CatalogCategoryEntity.php`
- `src/Repository/Catalog/CatalogCategoryRepository.php`
- `src/RepositoryInterface/Catalog/CatalogCategoryRepositoryInterface.php`
- `src/Controller/Catalog/CatalogCategoryController.php`
- `src/Event/Catalog/CatalogCategoryPublishedEvent.php`

## Machine rule

If a generator, agent, or automated refactor proposes a different tree or a different naming shape, the proposal is wrong and must be corrected before merge.
