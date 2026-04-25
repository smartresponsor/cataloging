# K11 Step08 — syndication destination foundation

## Goal

Add a narrow backend foundation for destination-aware category syndication.

## Added elements

- `src/Entity/CatalogSyndicationDestinationEntity.php`
- `src/EntityInterface/CatalogSyndicationDestinationEntityInterface.php`
- `src/Policy/CatalogSyndicationDestinationPolicy.php`
- `src/PolicyInterface/CatalogSyndicationDestinationPolicyInterface.php`
- `src/Repository/CatalogSyndicationDestinationRepository.php`
- `src/RepositoryInterface/CatalogSyndicationDestinationRepositoryInterface.php`
- `src/Service/CatalogSyndicationDestinationEntityService.php`
- `src/ServiceInterface/CatalogSyndicationDestinationEntityServiceInterface.php`
- `src/Event/CatalogSyndicationDestinationRegistered.php`
- `src/EventInterface/CatalogSyndicationDestinationRegisteredInterface.php`

## Why this wave exists

The component already has workflow, review, and publication quality foundations. This step makes syndication explicit by introducing destination registration and normalized destination metadata, while keeping the current architecture flat and Symfony-oriented.

## Guardrails preserved

- single Symfony-oriented application root
- `App\Cataloging\ -> src/`
- no `Port` / `Adaptor` / hexagonal skeleton
- no parallel application tree
- no domain-root wrapper restoration
