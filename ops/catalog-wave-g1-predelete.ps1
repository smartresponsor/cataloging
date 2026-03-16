$paths = @(
    'src/Service/CatalogCategory/Domain/Acl/AclPolicyService.php'
    'src/Service/CatalogCategory/Domain/Acl/AclRepositoryInterface.php'
    'src/Service/CatalogCategory/Domain/ApproxTotalService.php'
    'src/Service/CatalogCategory/Domain/CategoryInterface.php'
    'src/Service/CatalogCategory/Domain/CategoryRepositoryInterface.php'
    'src/Service/CatalogCategory/Domain/CategoryService.php'
    'src/Service/CatalogCategory/Domain/CollectionImportService.php'
    'src/Service/CatalogCategory/Domain/CollectionService.php'
    'src/Service/CatalogCategory/Domain/Graphql/CategoryLoader.php'
    'src/Service/CatalogCategory/Domain/Graphql/GraphqlGuard.php'
    'src/Service/CatalogCategory/Domain/Import/ImportRepositoryInterface.php'
    'src/Service/CatalogCategory/Domain/Import/ImportService.php'
    'src/Service/CatalogCategory/Domain/Quota/CacheStoreInterface.php'
    'src/Service/CatalogCategory/Domain/Quota/QuotaService.php'
    'src/Service/CatalogCategory/Domain/Quota/TokenBucket.php'
    'src/Service/CatalogCategory/Domain/Rule/RuleAdminService.php'
    'src/Service/CatalogCategory/Domain/Rule/RuleEngine.php'
    'src/Service/CatalogCategory/Domain/Rule/RuleRepositoryInterface.php'
    'src/Service/CatalogCategory/Domain/Seo/SeoRepositoryInterface.php'
    'src/Service/CatalogCategory/Domain/Suggest/RuleSuggestService.php'
    'src/ServiceInterface/CatalogCategory/Domain/CategoryServiceInterface.php'
    'src/ServiceInterface/CatalogCategory/Domain/CollectionImportServiceInterface.php'
    'src/ServiceInterface/CatalogCategory/Domain/CollectionServiceInterface.php'
    'src/Service/CatalogCategory/Domain'
    'src/ServiceInterface/CatalogCategory/Domain'
)

foreach ($rel in $paths) {
    $full = Join-Path $PSScriptRoot ('..\' + $rel.Replace('/', '\'))
    if (Test-Path $full) {
        Remove-Item $full -Recurse -Force
    }
}
