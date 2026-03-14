cataloging wave 041

Current cutover status:
- controller-folder runtime classes are being normalized to `App\Controller\Category`
- legacy FQCNs are preserved via `class_alias`
- physical file deletion is still deferred

Next target:
1. convert remaining controller files under `src/Controller/Category/*` with `App\Controller` namespace drift
2. normalize `src/Service/CatalogRepository.php` away from `SmartResponsor\Category\Layer\Repository`
3. normalize remaining legacy `SmartResponsor\Category` namespaces under `src/Api`, `src/Service`, `src/Repository`
4. then collapse delete-ready wrapper trees
