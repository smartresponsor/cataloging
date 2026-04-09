# K11 Step08 — syndication destination foundation

## Goal

Add a narrow backend foundation for destination-aware category syndication.

## Added elements

- `src/Entity/CategorySyndicationDestination.php`
- `src/EntityInterface/CategorySyndicationDestinationInterface.php`
- `src/Policy/CategorySyndicationDestinationPolicy.php`
- `src/PolicyInterface/CategorySyndicationDestinationPolicyInterface.php`
- `src/Repository/CategorySyndicationDestinationRepository.php`
- `src/RepositoryInterface/CategorySyndicationDestinationRepositoryInterface.php`
- `src/Service/CategorySyndicationDestinationService.php`
- `src/ServiceInterface/CategorySyndicationDestinationServiceInterface.php`
- `src/Event/CategorySyndicationDestinationRegistered.php`
- `src/EventInterface/CategorySyndicationDestinationRegisteredInterface.php`

## Why this wave exists

The component already has workflow, review, and publication quality foundations. This step makes syndication explicit by introducing destination registration and normalized destination metadata, while keeping the current architecture flat and Symfony-oriented.

## Guardrails preserved

- single Symfony-oriented application root
- `App\ -> src/`
- no `Port` / `Adaptor` / hexagonal skeleton
- no parallel application tree
- no domain-root wrapper restoration
