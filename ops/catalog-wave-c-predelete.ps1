$paths = @(
    'src/Service/Category.php'
    'src/Service/CategoryBreadcrumbBuilder.php'
    'src/Service/CategoryBulk.php'
    'src/Service/CategoryCacheHeader.php'
    'src/Service/CategoryCacheService.php'
    'src/Service/CategoryHttpCache.php'
    'src/Service/CategoryInterface.php'
    'src/Service/CategoryInvalidator.php'
    'src/Service/CategoryMoveInterface.php'
    'src/Service/CategoryMoveService.php'
    'src/Service/CategoryRepository.php'
    'src/Service/CategorySerializer.php'
    'src/Service/CategoryService.php'
    'src/Service/CategorySitemapGenerator.php'
    'src/Service/CategorySlugGenerator.php'
    'src/Service/Api/CategoryCacheHeader.php'
    'src/Service/Security/CategoryRole.php'
    'src/ServiceInterface/CategoryBreadcrumbBuilderInterface.php'
    'src/ServiceInterface/CategoryBulkInterface.php'
    'src/ServiceInterface/CategoryHttpCacheInterface.php'
    'src/ServiceInterface/CategoryInterface.php'
    'src/ServiceInterface/CategoryRepositoryInterface.php'
    'src/ServiceInterface/CategorySerializerInterface.php'
    'src/ServiceInterface/CategorySitemapGeneratorInterface.php'
    'src/ServiceInterface/CategorySlugGeneratorInterface.php'
    'src/Service/CategorySitemapGenerator-.php'
)

foreach ($rel in $paths) {
    $full = Join-Path $PSScriptRoot ('..\' + $rel.Replace('/', '\'))
    if (Test-Path $full) {
        Remove-Item $full -Force
    }
}
