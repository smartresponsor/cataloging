cataloging wave 042

Goal:
- remove the last confirmed `SmartResponsor\Category\Layer\Repository` namespace from runtime code
- keep old FQCN alive through `class_alias`

Canonical owner:
- src/Service/CatalogRepository.php -> App\Service\Query\Category\CategoryEntityRepositoryInterface
