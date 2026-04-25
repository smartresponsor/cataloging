# Catalog wave K11 step12 — syndication history and audit consolidation

This wave adds destination-specific publish history and recovery/audit consolidation without introducing a parallel orchestration tree.

## Added files

- `src/ValueObject/CatalogSyndicationDestinationHistory.php`
- `src/ValueObjectInterface/CatalogSyndicationDestinationHistoryInterface.php`
- `src/ValueObject/CategorySyndicationRecoveryAuditSummary.php`
- `src/ValueObjectInterface/CategorySyndicationRecoveryAuditSummaryInterface.php`
- `src/Policy/CategorySyndicationHistoryPolicy.php`
- `src/PolicyInterface/CategorySyndicationHistoryPolicyInterface.php`
- `src/Service/CategorySyndicationHistoryService.php`
- `src/ServiceInterface/CategorySyndicationHistoryServiceInterface.php`
- `src/Event/CatalogSyndicationDestinationHistoryBuilt.php`
- `src/EventInterface/CatalogSyndicationDestinationHistoryBuiltInterface.php`
- `src/Event/CategorySyndicationRecoveryAuditConsolidated.php`
- `src/EventInterface/CategorySyndicationRecoveryAuditConsolidatedInterface.php`
- `tests/Category/CategorySyndicationHistoryServiceTest.php`
- `docs/category-syndication-history-backend.md`

## Canon note

The wave stays inside the current canonical Symfony-oriented layer pattern:

- `src/ValueObject` + `src/ValueObjectInterface`
- `src/Policy` + `src/PolicyInterface`
- `src/Service` + `src/ServiceInterface`
- `src/Event` + `src/EventInterface`

No `Port`, `Adaptor`, `Infra`, `Domain`, or wrapper-tree reintroduction was used.
