# Catalog wave C — inner-unit Category convergence

Scope:
- rename inner service/service-interface classes from `Category...` to `Category...`
- keep top-level component surface on `Catalog...`
- do not yet rename unrelated runtime classes outside the inner category service layer

Applied renames:
- `src/Service/Category.php` -> `src/Service/Category.php`
- `src/Service/CategoryBreadcrumbBuilder.php` -> `src/Service/CategoryBreadcrumbBuilder.php`
- `src/Service/CategoryBulk.php` -> `src/Service/CategoryBulk.php`
- `src/Service/CategoryCacheHeader.php` -> `src/Service/CategoryCacheHeader.php`
- `src/Service/CategoryCacheService.php` -> `src/Service/CategoryCacheService.php`
- `src/Service/CategoryHttpCache.php` -> `src/Service/CategoryHttpCache.php`
- `src/Service/CategoryInterface.php` -> `src/Service/CategoryInterface.php`
- `src/Service/CategoryInvalidator.php` -> `src/Service/CategoryInvalidator.php`
- `src/Service/CategoryMoveInterface.php` -> `src/Service/CategoryMoveInterface.php`
- `src/Service/CategoryMoveService.php` -> `src/Service/CategoryMoveService.php`
- `src/Service/CategoryRepository.php` -> `src/Service/CategoryRepository.php`
- `src/Service/CategorySerializer.php` -> `src/Service/CategorySerializer.php`
- `src/Service/CategoryService.php` -> `src/Service/CategoryService.php`
- `src/Service/CategorySitemapGenerator.php` -> `src/Service/CategorySitemapGenerator.php`
- `src/Service/CategorySlugGenerator.php` -> `src/Service/CategorySlugGenerator.php`
- `src/Service/Api/CategoryCacheHeader.php` -> `src/Service/Api/CategoryCacheHeader.php`
- `src/Service/Security/CategoryRole.php` -> `src/Service/Security/CategoryRole.php`
- `src/ServiceInterface/CategoryBreadcrumbBuilderInterface.php` -> `src/ServiceInterface/CategoryBreadcrumbBuilderInterface.php`
- `src/ServiceInterface/CategoryBulkInterface.php` -> `src/ServiceInterface/CategoryBulkInterface.php`
- `src/ServiceInterface/CategoryHttpCacheInterface.php` -> `src/ServiceInterface/CategoryHttpCacheInterface.php`
- `src/ServiceInterface/CategoryInterface.php` -> `src/ServiceInterface/CategoryInterface.php`
- `src/ServiceInterface/CategoryRepositoryInterface.php` -> `src/ServiceInterface/CategoryRepositoryInterface.php`
- `src/ServiceInterface/CategorySerializerInterface.php` -> `src/ServiceInterface/CategorySerializerInterface.php`
- `src/ServiceInterface/CategorySitemapGeneratorInterface.php` -> `src/ServiceInterface/CategorySitemapGeneratorInterface.php`
- `src/ServiceInterface/CategorySlugGeneratorInterface.php` -> `src/ServiceInterface/CategorySlugGeneratorInterface.php`
- delete malformed duplicate `src/Service/CategorySitemapGenerator-.php`
