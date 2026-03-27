# Service ↔ ServiceInterface mirroring audit

Date: 2026-03-27

## Rule checked

Interfaces must not live under `src/Service/...` and must be placed in a mirrored/symmetric path under `src/ServiceInterface/...`.

## Result

- **No PHP interfaces were found in `src/Service/...`**.
- **Symmetry is partially broken**: a subset of services is implemented in nested folders, while corresponding interfaces are still located one level higher (not mirrored by folder path).

## Detected non-mirrored pairs

| Service implementation | Current interface location | Proposed mirrored interface location |
|---|---|---|
| `src/Service/Category/Import/CategoryImportService.php` | `src/ServiceInterface/Category/CategoryImportServiceInterface.php` | `src/ServiceInterface/Category/Import/CategoryImportServiceInterface.php` |
| `src/Service/Category/Quota/CategoryQuotaService.php` | `src/ServiceInterface/Category/CategoryQuotaServiceInterface.php` | `src/ServiceInterface/Category/Quota/CategoryQuotaServiceInterface.php` |
| `src/Service/Category/Quota/CategoryTokenBucket.php` | `src/ServiceInterface/Category/CategoryTokenBucketInterface.php` | `src/ServiceInterface/Category/Quota/CategoryTokenBucketInterface.php` |
| `src/Service/Category/Acl/CategoryAclPolicyService.php` | `src/ServiceInterface/Category/CategoryAclPolicyServiceInterface.php` | `src/ServiceInterface/Category/Acl/CategoryAclPolicyServiceInterface.php` |
| `src/Service/Category/Suggest/CategoryRuleSuggestService.php` | `src/ServiceInterface/Category/CategoryRuleSuggestServiceInterface.php` | `src/ServiceInterface/Category/Suggest/CategoryRuleSuggestServiceInterface.php` |
| `src/Service/Category/Graphql/CategoryLoader.php` | `src/ServiceInterface/Category/CategoryLoaderInterface.php` | `src/ServiceInterface/Category/Graphql/CategoryLoaderInterface.php` |
| `src/Service/Category/Graphql/CategoryGraphqlGuard.php` | `src/ServiceInterface/Category/CategoryGraphqlGuardInterface.php` | `src/ServiceInterface/Category/Graphql/CategoryGraphqlGuardInterface.php` |
| `src/Service/Category/Rule/CategoryRuleAdminService.php` | `src/ServiceInterface/Category/CategoryRuleAdminServiceInterface.php` | `src/ServiceInterface/Category/Rule/CategoryRuleAdminServiceInterface.php` |
| `src/Service/Category/Rule/CategoryRuleEngine.php` | `src/ServiceInterface/Category/CategoryRuleEngineInterface.php` | `src/ServiceInterface/Category/Rule/CategoryRuleEngineInterface.php` |

## Additional naming inconsistencies to review separately

These are not folder-mirroring issues only, but naming/contract inconsistencies:

- `src/Service/CatalogMoveService.php` implements `CategoryMoveInterface`.
- `src/Service/EdgeClientFastly.php` and `src/Service/EdgeClientCloudflare.php` share `EdgeClientInterface`.

If strict 1:1 mirroring is required, these should be normalized (rename contracts or split provider-specific interfaces).

## Safe migration plan

1. Move interface files to mirrored folders under `src/ServiceInterface/...`.
2. Update interface namespaces to match new folders.
3. Update all `use` imports and type hints in services/controllers/tests.
4. Keep temporary `class_alias`-style compatibility only if external API stability is required.
5. Run full test suite and static analysis.
