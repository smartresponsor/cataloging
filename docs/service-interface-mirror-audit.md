# Service/ServiceInterface Mirror Audit

Date: 2026-03-24

## Current status
- `src/Service` PHP files: **157**
- `src/ServiceInterface` PHP files: **73**
- Interfaces declared directly in `src/Service`: **0** (target: 0)
- Services without mirrored interfaces: **97**
- Interfaces without mirrored services: **13**

## Findings
- No interfaces were found inside `src/Service`, so the strict folder rule is currently respected.
- Mirror symmetry is incomplete: many services do not have a corresponding interface in `src/ServiceInterface/...`.
- There are interface files that point to missing service implementations (or an implementation exists under another name/path).

## Proposed change plan
1. Add missing mirrored interfaces for services that are part of the public service contract (start with dependency-injected services used by controllers/handlers).
2. For orphan interfaces, either create the mirrored service class or delete/rename the interface to match the actual implementation path.
3. Keep this check in CI: run `php tools/check-service-interface-mirror.php` and fail when summary counters are non-zero (except temporary allowlist, if needed).
4. Perform the migration in small batches (10–20 services per commit) to keep code review manageable and avoid broad container wiring regressions.

## Orphan interfaces (need alignment)
- `src/ServiceInterface/Acl/AclRepositoryInterface.php` → expected service `src/Service/Acl/AclRepository.php`
- `src/ServiceInterface/Api/GraphqlResolverInterface.php` → expected service `src/Service/Api/GraphqlResolver.php`
- `src/ServiceInterface/CategoryMoveInterface.php` → expected service `src/Service/CategoryMove.php`
- `src/ServiceInterface/CategoryRepositoryInterface.php` → expected service `src/Service/CategoryRepository.php`
- `src/ServiceInterface/EdgeClientInterface.php` → expected service `src/Service/EdgeClient.php`
- `src/ServiceInterface/Import/ImportRepositoryInterface.php` → expected service `src/Service/Import/ImportRepository.php`
- `src/ServiceInterface/Integration/WebhookClientInterface.php` → expected service `src/Service/Integration/WebhookClient.php`
- `src/ServiceInterface/Quota/CacheStoreInterface.php` → expected service `src/Service/Quota/CacheStore.php`
- `src/ServiceInterface/Rule/RuleRepositoryInterface.php` → expected service `src/Service/Rule/RuleRepository.php`
- `src/ServiceInterface/Security/OidcJwtVerifierInterface.php` → expected service `src/Service/Security/OidcJwtVerifier.php`
- `src/ServiceInterface/Security/TenantRolePolicyInterface.php` → expected service `src/Service/Security/TenantRolePolicy.php`
- `src/ServiceInterface/Seo/CanonicalPolicyInterface.php` → expected service `src/Service/Seo/CanonicalPolicy.php`
- `src/ServiceInterface/Seo/SeoRepositoryInterface.php` → expected service `src/Service/Seo/SeoRepository.php`

## Sample missing mirrors (first 40 of 97)
- `src/Service/AbVariantResolver.php` → expected interface `src/ServiceInterface/AbVariantResolverInterface.php`
- `src/Service/AliasRouter.php` → expected interface `src/ServiceInterface/AliasRouterInterface.php`
- `src/Service/CatalogAttachmentService.php` → expected interface `src/ServiceInterface/CatalogAttachmentServiceInterface.php`
- `src/Service/CatalogBatchImportRunnerService.php` → expected interface `src/ServiceInterface/CatalogBatchImportRunnerServiceInterface.php`
- `src/Service/CatalogBillingTagEmitterService.php` → expected interface `src/ServiceInterface/CatalogBillingTagEmitterServiceInterface.php`
- `src/Service/CatalogBulkOperatorService.php` → expected interface `src/ServiceInterface/CatalogBulkOperatorServiceInterface.php`
- `src/Service/CacheInvalidator.php` → expected interface `src/ServiceInterface/CacheInvalidatorInterface.php`
- `src/Service/CatalogCacheMetricsCollectorService.php` → expected interface `src/ServiceInterface/CatalogCacheMetricsCollectorServiceInterface.php`
- `src/Service/CanonicalPolicyLocale.php` → expected interface `src/ServiceInterface/CanonicalPolicyLocaleInterface.php`
- `src/Service/CatalogCanonicalResolverService.php` → expected interface `src/ServiceInterface/CatalogCanonicalResolverServiceInterface.php`
- `src/Service/Category/Acl/AclPolicyService.php` → expected interface `src/ServiceInterface/Category/Acl/AclPolicyServiceInterface.php`
- `src/Service/Category/ApproxTotalService.php` → expected interface `src/ServiceInterface/Category/ApproxTotalServiceInterface.php`
- `src/Service/Category/Graphql/CategoryLoader.php` → expected interface `src/ServiceInterface/Category/Graphql/CategoryLoaderInterface.php`
- `src/Service/Category/Graphql/GraphqlGuard.php` → expected interface `src/ServiceInterface/Category/Graphql/GraphqlGuardInterface.php`
- `src/Service/Category/Import/ImportService.php` → expected interface `src/ServiceInterface/Category/Import/ImportServiceInterface.php`
- `src/Service/Category/Quota/QuotaService.php` → expected interface `src/ServiceInterface/Category/Quota/QuotaServiceInterface.php`
- `src/Service/Category/Quota/TokenBucket.php` → expected interface `src/ServiceInterface/Category/Quota/TokenBucketInterface.php`
- `src/Service/Category/Rule/RuleAdminService.php` → expected interface `src/ServiceInterface/Category/Rule/RuleAdminServiceInterface.php`
- `src/Service/Category/Rule/RuleEngine.php` → expected interface `src/ServiceInterface/Category/Rule/RuleEngineInterface.php`
- `src/Service/Category/Suggest/RuleSuggestService.php` → expected interface `src/ServiceInterface/Category/Suggest/RuleSuggestServiceInterface.php`
- `src/Service/CategoryCacheHeader.php` → expected interface `src/ServiceInterface/CategoryCacheHeaderInterface.php`
- `src/Service/CategoryCacheService.php` → expected interface `src/ServiceInterface/CategoryCacheServiceInterface.php`
- `src/Service/CategoryInvalidator.php` → expected interface `src/ServiceInterface/CategoryInvalidatorInterface.php`
- `src/Service/CategoryMoveService.php` → expected interface `src/ServiceInterface/CategoryMoveServiceInterface.php`
- `src/Service/ChannelFilter.php` → expected interface `src/ServiceInterface/ChannelFilterInterface.php`
- `src/Service/CatalogCircuitBreakerService.php` → expected interface `src/ServiceInterface/CatalogCircuitBreakerServiceInterface.php`
- `src/Service/CatalogCloudflarePurgerService.php` → expected interface `src/ServiceInterface/CatalogCloudflarePurgerServiceInterface.php`
- `src/Service/CatalogCollectionBuilderService.php` → expected interface `src/ServiceInterface/CatalogCollectionBuilderServiceInterface.php`
- `src/Service/DataResidencyGuard.php` → expected interface `src/ServiceInterface/DataResidencyGuardInterface.php`
- `src/Service/CatalogDlqService.php` → expected interface `src/ServiceInterface/CatalogDlqServiceInterface.php`
- `src/Service/DraftPolicy.php` → expected interface `src/ServiceInterface/DraftPolicyInterface.php`
- `src/Service/CatalogEdgeClientCloudflareService.php` → expected interface `src/ServiceInterface/CatalogEdgeClientCloudflareServiceInterface.php`
- `src/Service/CatalogEdgeClientFastlyService.php` → expected interface `src/ServiceInterface/CatalogEdgeClientFastlyServiceInterface.php`
- `src/Service/CatalogEtagGeneratorService.php` → expected interface `src/ServiceInterface/CatalogEtagGeneratorServiceInterface.php`
- `src/Service/EtagMiddleware.php` → expected interface `src/ServiceInterface/EtagMiddlewareInterface.php`
- `src/Service/FacetFilter.php` → expected interface `src/ServiceInterface/FacetFilterInterface.php`
- `src/Service/CatalogFacetIndexBuilderService.php` → expected interface `src/ServiceInterface/CatalogFacetIndexBuilderServiceInterface.php`
- `src/Service/FacetRank.php` → expected interface `src/ServiceInterface/FacetRankInterface.php`
- `src/Service/CatalogFacetSearchService.php` → expected interface `src/ServiceInterface/CatalogFacetSearchServiceInterface.php`
- `src/Service/CatalogFacetSearchAdvancedService.php` → expected interface `src/ServiceInterface/CatalogFacetSearchAdvancedServiceInterface.php`
