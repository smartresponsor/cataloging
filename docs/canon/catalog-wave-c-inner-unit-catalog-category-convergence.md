# Catalog wave C — inner-unit Catalogtests convergence

Scope:
- rename inner service/service-interface classes from `tests...` to `Catalogtests...`
- keep top-level component surface on `Catalog...`
- do not yet rename unrelated runtime classes outside the inner category service layer

Applied renames:
- `src/Service/tests.php` -> `src/Service/Catalogtests.php`
- `src/Service/testsBreadcrumbBuilder.php` -> `src/Service/CatalogtestsBreadcrumbBuilder.php`
- `src/Service/testsBulk.php` -> `src/Service/CatalogtestsBulk.php`
- `src/Service/testsCacheHeader.php` -> `src/Service/CatalogtestsCacheHeader.php`
- `src/Service/testsCacheService.php` -> `src/Service/CatalogtestsCacheService.php`
- `src/Service/testsHttpCache.php` -> `src/Service/CatalogtestsHttpCache.php`
- `src/Service/testsInterface.php` -> `src/Service/CatalogtestsInterface.php`
- `src/Service/testsInvalidator.php` -> `src/Service/CatalogtestsInvalidator.php`
- `src/Service/testsMoveInterface.php` -> `src/Service/CatalogtestsMoveInterface.php`
- `src/Service/testsMoveService.php` -> `src/Service/CatalogtestsMoveService.php`
- `src/Service/testsRepository.php` -> `src/Service/CatalogtestsRepository.php`
- `src/Service/testsSerializer.php` -> `src/Service/CatalogtestsSerializer.php`
- `src/Service/testsService.php` -> `src/Service/CatalogtestsService.php`
- `src/Service/testsSitemapGenerator.php` -> `src/Service/CatalogtestsSitemapGenerator.php`
- `src/Service/testsSlugGenerator.php` -> `src/Service/CatalogtestsSlugGenerator.php`
- `src/Service/Api/testsCacheHeader.php` -> `src/Service/Api/CatalogtestsCacheHeader.php`
- `src/Service/Security/testsRole.php` -> `src/Service/Security/CatalogtestsRole.php`
- `src/ServiceInterface/testsBreadcrumbBuilderInterface.php` -> `src/ServiceInterface/CatalogtestsBreadcrumbBuilderInterface.php`
- `src/ServiceInterface/testsBulkInterface.php` -> `src/ServiceInterface/CatalogtestsBulkInterface.php`
- `src/ServiceInterface/testsHttpCacheInterface.php` -> `src/ServiceInterface/CatalogtestsHttpCacheInterface.php`
- `src/ServiceInterface/testsInterface.php` -> `src/ServiceInterface/CatalogtestsInterface.php`
- `src/ServiceInterface/testsRepositoryInterface.php` -> `src/ServiceInterface/CatalogtestsRepositoryInterface.php`
- `src/ServiceInterface/testsSerializerInterface.php` -> `src/ServiceInterface/CatalogtestsSerializerInterface.php`
- `src/ServiceInterface/testsSitemapGeneratorInterface.php` -> `src/ServiceInterface/CatalogtestsSitemapGeneratorInterface.php`
- `src/ServiceInterface/testsSlugGeneratorInterface.php` -> `src/ServiceInterface/CatalogtestsSlugGeneratorInterface.php`
- delete malformed duplicate `src/Service/testsSitemapGenerator-.php`
