# Catalog Wave C — remaining wrapper resolution

Base: `cataloging-197-current-repository-wave-b-wrapper-flattened.zip`

This wave resolves the last remaining live wrapper directories:
- `src/Controller/Category/**`
- `src/Event/Category/**`

It also fixes the known syntax tail in `src/Controller/CategoryMerchController.php`.

## Applied moves
- `src/Controller/Category/Admin/CategoryAdminController.php` -> `src/Controller/Admin/CategoryAdminController.php`
- `src/Controller/Category/Admin/CategoryAuditController.php` -> `src/Controller/Admin/CategoryAuditController.php`
- `src/Controller/Category/Admin/CategoryBatchEditController.php` -> `src/Controller/Admin/CategoryBatchEditController.php`
- `src/Controller/Category/Admin/CategoryBulkController.php` -> `src/Controller/Admin/CategoryBulkController.php`
- `src/Controller/Category/Admin/CategoryDlqController.php` -> `src/Controller/Admin/CategoryDlqController.php`
- `src/Controller/Category/Admin/CategoryListController.php` -> `src/Controller/Admin/CategoryListController.php`
- `src/Controller/Category/Admin/CategoryMoveController.php` -> `src/Controller/Admin/CategoryMoveController.php`
- `src/Controller/Category/Admin/CategoryOpsController.php` -> `src/Controller/Admin/CategoryOpsController.php`
- `src/Controller/Category/Admin/CategoryPermsController.php` -> `src/Controller/Admin/CategoryPermsController.php`
- `src/Controller/Category/Api/CategoryAdminApiController.php` -> `src/Controller/Api/CategoryAdminApiController.php`
- `src/Controller/Category/Merchant/CategoryMerchantController.php` -> `src/Controller/Merchant/CategoryMerchantController.php`
- `src/Event/Category/CategoryMoved.php` -> `src/Event/CategoryReordered.php`

## Removed wrapper dirs
- `src/Controller/Category/Admin`
- `src/Controller/Category/Api`
- `src/Controller/Category/Merchant`
- `src/Controller/Category`
- `src/Event/Category`
