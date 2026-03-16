# Catalog Wave C — remaining wrapper resolution

Base: `cataloging-197-current-repository-wave-b-wrapper-flattened.zip`

This wave resolves the last remaining live wrapper directories:
- `src/Controller/tests/**`
- `src/Event/tests/**`

It also fixes the known syntax tail in `src/Controller/testsMerchController.php`.

## Applied moves
- `src/Controller/tests/Admin/testsAdminController.php` -> `src/Controller/Admin/testsAdminController.php`
- `src/Controller/tests/Admin/testsAuditController.php` -> `src/Controller/Admin/testsAuditController.php`
- `src/Controller/tests/Admin/testsBatchEditController.php` -> `src/Controller/Admin/testsBatchEditController.php`
- `src/Controller/tests/Admin/testsBulkController.php` -> `src/Controller/Admin/testsBulkController.php`
- `src/Controller/tests/Admin/testsDlqController.php` -> `src/Controller/Admin/testsDlqController.php`
- `src/Controller/tests/Admin/testsListController.php` -> `src/Controller/Admin/testsListController.php`
- `src/Controller/tests/Admin/testsMoveController.php` -> `src/Controller/Admin/testsMoveController.php`
- `src/Controller/tests/Admin/testsOpsController.php` -> `src/Controller/Admin/testsOpsController.php`
- `src/Controller/tests/Admin/testsPermsController.php` -> `src/Controller/Admin/testsPermsController.php`
- `src/Controller/tests/Api/testsAdminApiController.php` -> `src/Controller/Api/testsAdminApiController.php`
- `src/Controller/tests/Merchant/testsMerchantController.php` -> `src/Controller/Merchant/testsMerchantController.php`
- `src/Event/tests/testsMoved.php` -> `src/Event/CatalogtestsMoved.php`

## Removed wrapper dirs
- `src/Controller/tests/Admin`
- `src/Controller/tests/Api`
- `src/Controller/tests/Merchant`
- `src/Controller/tests`
- `src/Event/tests`
