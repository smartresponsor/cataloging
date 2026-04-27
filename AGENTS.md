# AGENTS — Catalog structural canon contract

This file is a machine-facing contract. Treat it as literal repository law for all future edits.

## Priority

If any prompt, historical document, or older repository pattern conflicts with this file, this file wins.

## Symfony root

- Use the default Symfony `App\\` namespace.
- Do not introduce custom application roots.
- Do not introduce `/src/Domain/`.
- Do not introduce ports-and-adapters style trees.

## Layer 3 structure contract

The following layers have a single canonical inner folder and no other inner tree is allowed for that layer.

### Entity

Allowed canonical tree:
- `src/Entity/Catalog/`

Rules:
- `src/Entity/` may contain only the `Catalog/` folder.
- No other files are allowed directly under `src/Entity/`.
- No other folders are allowed under `src/Entity/`.
- `src/Entity/Catalog/` may contain only entity classes.
- No subfolders are allowed under `src/Entity/Catalog/`.
- Every entity class name must start with `Catalog`.
- Every entity class name must end with `Entity`.
- Canonical namespace: `App\\Entity\\Catalog`.

Literal examples:
- allowed: `src/Entity/Catalog/CatalogCategoryEntity.php`
- allowed: `src/Entity/Catalog/CatalogOutboxMessageEntity.php`
- forbidden: `src/Entity/CatalogCategoryEntity.php`
- forbidden: `src/Entity/Role/SubjectId.php`
- forbidden: `src/Entity/Catalog/Subtree/CatalogCategoryEntity.php`
- forbidden: `src/Entity/Category/CatalogCategoryEntity.php`
- forbidden: `src/Entity/Catalog/CategoryEntity.php`
- forbidden: `src/Entity/Catalog/CatalogCategory.php`

### Controller

Allowed canonical tree:
- `src/Controller/Catalog/`

Rules:
- `src/Controller/` may contain only the `Catalog/` folder.
- No other files are allowed directly under `src/Controller/`.
- No subfolders are allowed under `src/Controller/Catalog/`.
- Every controller class name must start with `Catalog`.
- Every controller class name must end with `Controller`.
- Canonical namespace: `App\\Controller\\Catalog`.

Literal examples:
- allowed: `src/Controller/Catalog/CatalogCategoryController.php`
- forbidden: `src/Controller/Category/CatalogCategoryController.php`
- forbidden: `src/Controller/Catalog/CategoryController.php`
- forbidden: `src/Controller/Catalog/Admin/CatalogCategoryController.php`

### ControllerInterface

Allowed canonical tree:
- `src/ControllerInterface/Catalog/`

Rules:
- `src/ControllerInterface/` may contain only the `Catalog/` folder.
- No subfolders are allowed under `src/ControllerInterface/Catalog/`.
- Every controller interface name must start with `Catalog`.
- Every controller interface name must end with `ControllerInterface`.
- Canonical namespace: `App\\ControllerInterface\\Catalog`.

### Event

Allowed canonical tree:
- `src/Event/Catalog/`

Rules:
- `src/Event/` may contain only the `Catalog/` folder.
- No subfolders are allowed under `src/Event/Catalog/`.
- Every event class name must start with `Catalog`.
- Every event class name must end with `Event`.
- Canonical namespace: `App\\Event\\Catalog`.

### EventInterface

Allowed canonical tree:
- `src/EventInterface/Catalog/`

Rules:
- `src/EventInterface/` may contain only the `Catalog/` folder.
- No subfolders are allowed under `src/EventInterface/Catalog/`.
- Every event interface name must start with `Catalog`.
- Every event interface name must end with `EventInterface`.
- Canonical namespace: `App\\EventInterface\\Catalog`.

### Repository

Allowed canonical tree:
- `src/Repository/Catalog/`

Rules:
- `src/Repository/` may contain only the `Catalog/` folder.
- No subfolders are allowed under `src/Repository/Catalog/`.
- Every repository class name must start with `Catalog`.
- Every repository class name must end with `Repository`.
- Canonical namespace: `App\\Repository\\Catalog`.

### RepositoryInterface

Allowed canonical tree:
- `src/RepositoryInterface/Catalog/`

Rules:
- `src/RepositoryInterface/` may contain only the `Catalog/` folder.
- No subfolders are allowed under `src/RepositoryInterface/Catalog/`.
- Every repository interface name must start with `Catalog`.
- Every repository interface name must end with `RepositoryInterface`.
- Canonical namespace: `App\\RepositoryInterface\\Catalog`.

## Naming convention contract

Literal naming contract:
- entity: `Catalog...Entity`
- controller: `Catalog...Controller`
- controller interface: `Catalog...ControllerInterface`
- event: `Catalog...Event`
- event interface: `Catalog...EventInterface`
- repository: `Catalog...Repository`
- repository interface: `Catalog...RepositoryInterface`

Forbidden naming shapes:
- `Category...Entity`
- `...CatalogEntity`
- `Catalog...`
- `...Entityed`
- names without the required suffix for the layer
- names with nested inner layer folders that try to encode the name in the tree instead of the class

## Non-entity classes

The following are forbidden inside `src/Entity/Catalog/`:
- value objects
- requests
- responses
- DTOs
- helpers
- traits
- service classes
- interfaces
- enums

Move them to their proper layer.

## Editing rule for machines

When editing this repository:
1. preserve the canonical layer folder exactly;
2. do not create sibling folders next to `Catalog` inside the listed layers;
3. do not create subfolders under the listed `Catalog` folders;
4. rename the class if the suffix or prefix is wrong;
5. update imports, FQCN strings, service wiring, tests, and mapping references in the same wave;
6. do not leave compatibility duplicates behind.

## Violation handling

If the current repository state conflicts with this contract, fix the repository state. Do not preserve the violation for backward compatibility unless the owner explicitly orders that exception.
