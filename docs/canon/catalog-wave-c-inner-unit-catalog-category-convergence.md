# Catalog wave C — inner-unit CatalogCategory convergence

Scope:
- rename inner service/service-interface classes from `Category...` to `CatalogCategory...`
- keep top-level component surface on `Catalog...`
- do not yet rename unrelated runtime classes outside the inner category service layer

Applied renames:
- `src/Service/Category.php` -> `src/Service/CatalogCategory.php`
- `src/Service/CategoryBreadcrumbBuilder.php` -> `src/Service/CatalogCategoryBreadcrumbBuilder.php`
- `src/Service/CategoryBulk.php` -> `src/Service/CatalogCategoryBulk.php`
- `src/Service/CategoryCacheHeader.php` -> `src/Service/CatalogCategoryCacheHeader.php`
- `src/Service/CategoryCacheService.php` -> `src/Service/CatalogCategoryCacheService.php`
- `src/Service/CategoryHttpCache.php` -> `src/Service/CatalogCategoryHttpCache.php`
- `src/Service/CategoryInterface.php` -> `src/Service/CatalogCategoryInterface.php`
- `src/Service/CategoryInvalidator.php` -> `src/Service/CatalogCategoryInvalidator.php`
- `src/Service/CategoryMoveInterface.php` -> `src/Service/CatalogCategoryMoveInterface.php`
- `src/Service/CategoryMoveService.php` -> `src/Service/CatalogCategoryMoveService.php`
- `src/Service/CategoryRepository.php` -> `src/Service/CatalogCategoryRepository.php`
- `src/Service/CategorySerializer.php` -> `src/Service/CatalogCategorySerializer.php`
- `src/Service/CategoryService.php` -> `src/Service/CatalogCategoryService.php`
- `src/Service/CategorySitemapGenerator.php` -> `src/Service/CatalogCategorySitemapGenerator.php`
- `src/Service/CategorySlugGenerator.php` -> `src/Service/CatalogCategorySlugGenerator.php`
- `src/Service/Api/CategoryCacheHeader.php` -> `src/Service/Api/CatalogCategoryCacheHeader.php`
- `src/Service/Security/CategoryRole.php` -> `src/Service/Security/CatalogCategoryRole.php`
- `src/ServiceInterface/CategoryBreadcrumbBuilderInterface.php` -> `src/ServiceInterface/CatalogCategoryBreadcrumbBuilderInterface.php`
- `src/ServiceInterface/CategoryBulkInterface.php` -> `src/ServiceInterface/CatalogCategoryBulkInterface.php`
- `src/ServiceInterface/CategoryHttpCacheInterface.php` -> `src/ServiceInterface/CatalogCategoryHttpCacheInterface.php`
- `src/ServiceInterface/CategoryInterface.php` -> `src/ServiceInterface/CatalogCategoryInterface.php`
- `src/ServiceInterface/CategoryRepositoryInterface.php` -> `src/ServiceInterface/CatalogCategoryRepositoryInterface.php`
- `src/ServiceInterface/CategorySerializerInterface.php` -> `src/ServiceInterface/CatalogCategorySerializerInterface.php`
- `src/ServiceInterface/CategorySitemapGeneratorInterface.php` -> `src/ServiceInterface/CatalogCategorySitemapGeneratorInterface.php`
- `src/ServiceInterface/CategorySlugGeneratorInterface.php` -> `src/ServiceInterface/CatalogCategorySlugGeneratorInterface.php`
- delete malformed duplicate `src/Service/CategorySitemapGenerator-.php`
