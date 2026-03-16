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
- `src/Domain/tests/Acl/AclPolicyService.php` -> `src/Service/Catalogtests/Domain/Acl/AclPolicyService.php`
- `src/Domain/tests/Acl/AclRepositoryInterface.php` -> `src/Service/Catalogtests/Domain/Acl/AclRepositoryInterface.php`
- `src/Domain/tests/ApproxTotalService.php` -> `src/Service/Catalogtests/Domain/ApproxTotalService.php`
- `src/Domain/tests/tests.php` -> `src/Layer/Domain/tests.php`
- `src/Domain/tests/testsInterface.php` -> `src/Service/Catalogtests/Domain/testsInterface.php`
- `src/Domain/tests/testsRepositoryInterface.php` -> `src/Service/Catalogtests/Domain/testsRepositoryInterface.php`
- `src/Domain/tests/testsService.php` -> `src/Service/Catalogtests/Domain/testsService.php`
- `src/Domain/tests/CollectionImportService.php` -> `src/Service/Catalogtests/Domain/CollectionImportService.php`
- `src/Domain/tests/CollectionService.php` -> `src/Service/Catalogtests/Domain/CollectionService.php`
- `src/Domain/tests/Graphql/testsLoader.php` -> `src/Service/Catalogtests/Domain/Graphql/testsLoader.php`
- `src/Domain/tests/Graphql/GraphqlGuard.php` -> `src/Service/Catalogtests/Domain/Graphql/GraphqlGuard.php`
- `src/Domain/tests/Import/ImportRepositoryInterface.php` -> `src/Service/Catalogtests/Domain/Import/ImportRepositoryInterface.php`
- `src/Domain/tests/Import/ImportService.php` -> `src/Service/Catalogtests/Domain/Import/ImportService.php`
- `src/Domain/tests/Quota/CacheStoreInterface.php` -> `src/Service/Catalogtests/Domain/Quota/CacheStoreInterface.php`
- `src/Domain/tests/Quota/QuotaService.php` -> `src/Service/Catalogtests/Domain/Quota/QuotaService.php`
- `src/Domain/tests/Quota/TokenBucket.php` -> `src/Service/Catalogtests/Domain/Quota/TokenBucket.php`
- `src/Domain/tests/Rule/RuleAdminService.php` -> `src/Service/Catalogtests/Domain/Rule/RuleAdminService.php`
- `src/Domain/tests/Rule/RuleEngine.php` -> `src/Service/Catalogtests/Domain/Rule/RuleEngine.php`
- `src/Domain/tests/Rule/RuleRepositoryInterface.php` -> `src/Service/Catalogtests/Domain/Rule/RuleRepositoryInterface.php`
- `src/Domain/tests/Seo/SeoRepositoryInterface.php` -> `src/Service/Catalogtests/Domain/Seo/SeoRepositoryInterface.php`
- `src/Domain/tests/Suggest/RuleSuggestService.php` -> `src/Service/Catalogtests/Domain/Suggest/RuleSuggestService.php`
- `src/DomainInterface/tests/testsServiceInterface.php` -> `src/ServiceInterface/Catalogtests/Domain/testsServiceInterface.php`
- `src/DomainInterface/tests/CollectionImportServiceInterface.php` -> `src/ServiceInterface/Catalogtests/Domain/CollectionImportServiceInterface.php`
- `src/DomainInterface/tests/CollectionServiceInterface.php` -> `src/ServiceInterface/Catalogtests/Domain/CollectionServiceInterface.php`
- `src/Infra/tests/CacheInvalidator.php` -> `src/Infrastructure/CacheInvalidator.php`
- `src/Infra/tests/testsAuditLogger.php` -> `src/Infrastructure/testsAuditLogger.php`
- `src/Infra/tests/HttpWebhookSender.php` -> `src/Infrastructure/HttpWebhookSender.php`
- `src/Infra/tests/MessengerOutboxDispatcher.php` -> `src/Infrastructure/MessengerOutboxDispatcher.php`
- `src/Infra/tests/OrderWebhookPublisher.php` -> `src/Infrastructure/OrderWebhookPublisher.php`
- `src/Infra/tests/ProductWebhookPublisher.php` -> `src/Infrastructure/ProductWebhookPublisher.php`
- `src/InfraInterface/tests/OutboxDispatcherInterface.php` -> `src/InfrastructureInterface/OutboxDispatcherInterface.php`
- `src/InfraInterface/tests/WebhookSenderInterface.php` -> `src/InfrastructureInterface/WebhookSenderInterface.php`
- `src/Adapter/tests/StorefrontAdapter.php` -> `src/Service/StorefrontAdapter.php`
- `src/Http/tests/WebhookController.php` -> `src/Controller/WebhookController.php`
- `src/Http/tests/SecurityHeaderListener.php` -> `src/EventSubscriber/SecurityHeaderListener.php`
