# Catalog Wave A — root layer evacuation

Base: current user slice `Cataloging.zip`

This wave evacuates non-canonical root layers out of:
- `src/Domain`
- `src/DomainInterface`
- `src/Infra`
- `src/InfraInterface`
- `src/Adapter`
- `src/Http`

## Strategy

- move files into canonical layer-aligned paths
- update namespaces/imports
- fix `composer.json` so the slice is a valid baseline again
- keep semantics conservative; this is a structural parse wave, not a full naming overhaul

## Moved entries
- `src/Domain/Category/Acl/AclPolicyService.php` -> `src/Service/CatalogCategory/Domain/Acl/AclPolicyService.php`
- `src/Domain/Category/Acl/AclRepositoryInterface.php` -> `src/Service/CatalogCategory/Domain/Acl/AclRepositoryInterface.php`
- `src/Domain/Category/ApproxTotalService.php` -> `src/Service/CatalogCategory/Domain/ApproxTotalService.php`
- `src/Domain/Category/Category.php` -> `src/Layer/Domain/Category.php`
- `src/Domain/Category/CategoryInterface.php` -> `src/Service/CatalogCategory/Domain/CategoryInterface.php`
- `src/Domain/Category/CategoryRepositoryInterface.php` -> `src/Service/CatalogCategory/Domain/CategoryRepositoryInterface.php`
- `src/Domain/Category/CategoryService.php` -> `src/Service/CatalogCategory/Domain/CategoryService.php`
- `src/Domain/Category/CollectionImportService.php` -> `src/Service/CatalogCategory/Domain/CollectionImportService.php`
- `src/Domain/Category/CollectionService.php` -> `src/Service/CatalogCategory/Domain/CollectionService.php`
- `src/Domain/Category/Graphql/CategoryLoader.php` -> `src/Service/CatalogCategory/Domain/Graphql/CategoryLoader.php`
- `src/Domain/Category/Graphql/GraphqlGuard.php` -> `src/Service/CatalogCategory/Domain/Graphql/GraphqlGuard.php`
- `src/Domain/Category/Import/ImportRepositoryInterface.php` -> `src/Service/CatalogCategory/Domain/Import/ImportRepositoryInterface.php`
- `src/Domain/Category/Import/ImportService.php` -> `src/Service/CatalogCategory/Domain/Import/ImportService.php`
- `src/Domain/Category/Quota/CacheStoreInterface.php` -> `src/Service/CatalogCategory/Domain/Quota/CacheStoreInterface.php`
- `src/Domain/Category/Quota/QuotaService.php` -> `src/Service/CatalogCategory/Domain/Quota/QuotaService.php`
- `src/Domain/Category/Quota/TokenBucket.php` -> `src/Service/CatalogCategory/Domain/Quota/TokenBucket.php`
- `src/Domain/Category/Rule/RuleAdminService.php` -> `src/Service/CatalogCategory/Domain/Rule/RuleAdminService.php`
- `src/Domain/Category/Rule/RuleEngine.php` -> `src/Service/CatalogCategory/Domain/Rule/RuleEngine.php`
- `src/Domain/Category/Rule/RuleRepositoryInterface.php` -> `src/Service/CatalogCategory/Domain/Rule/RuleRepositoryInterface.php`
- `src/Domain/Category/Seo/SeoRepositoryInterface.php` -> `src/Service/CatalogCategory/Domain/Seo/SeoRepositoryInterface.php`
- `src/Domain/Category/Suggest/RuleSuggestService.php` -> `src/Service/CatalogCategory/Domain/Suggest/RuleSuggestService.php`
- `src/DomainInterface/Category/CategoryServiceInterface.php` -> `src/ServiceInterface/CatalogCategory/Domain/CategoryServiceInterface.php`
- `src/DomainInterface/Category/CollectionImportServiceInterface.php` -> `src/ServiceInterface/CatalogCategory/Domain/CollectionImportServiceInterface.php`
- `src/DomainInterface/Category/CollectionServiceInterface.php` -> `src/ServiceInterface/CatalogCategory/Domain/CollectionServiceInterface.php`
- `src/Infra/Category/CacheInvalidator.php` -> `src/Infrastructure/CacheInvalidator.php`
- `src/Infra/Category/CategoryAuditLogger.php` -> `src/Infrastructure/CategoryAuditLogger.php`
- `src/Infra/Category/HttpWebhookSender.php` -> `src/Infrastructure/HttpWebhookSender.php`
- `src/Infra/Category/MessengerOutboxDispatcher.php` -> `src/Infrastructure/MessengerOutboxDispatcher.php`
- `src/Infra/Category/OrderWebhookPublisher.php` -> `src/Infrastructure/OrderWebhookPublisher.php`
- `src/Infra/Category/ProductWebhookPublisher.php` -> `src/Infrastructure/ProductWebhookPublisher.php`
- `src/InfraInterface/Category/OutboxDispatcherInterface.php` -> `src/InfrastructureInterface/OutboxDispatcherInterface.php`
- `src/InfraInterface/Category/WebhookSenderInterface.php` -> `src/InfrastructureInterface/WebhookSenderInterface.php`
- `src/Adapter/Category/StorefrontAdapter.php` -> `src/Service/StorefrontAdapter.php`
- `src/Http/Category/WebhookController.php` -> `src/Controller/WebhookController.php`
- `src/Http/Category/SecurityHeaderListener.php` -> `src/EventSubscriber/SecurityHeaderListener.php`
