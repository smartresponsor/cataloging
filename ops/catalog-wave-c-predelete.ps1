$paths = @(
    'src/Controller/Category/Admin/CategoryAdminController.php'
    'src/Controller/Category/Admin/CategoryAuditController.php'
    'src/Controller/Category/Admin/CategoryBatchEditController.php'
    'src/Controller/Category/Admin/CategoryBulkController.php'
    'src/Controller/Category/Admin/CategoryDlqController.php'
    'src/Controller/Category/Admin/CategoryListController.php'
    'src/Controller/Category/Admin/CategoryMoveController.php'
    'src/Controller/Category/Admin/CategoryOpsController.php'
    'src/Controller/Category/Admin/CategoryPermsController.php'
    'src/Controller/Category/Api/CategoryAdminApiController.php'
    'src/Controller/Category/Merchant/CategoryMerchantController.php'
    'src/Event/Category/CategoryMoved.php'
    'src/Controller/Category/Admin'
    'src/Controller/Category/Api'
    'src/Controller/Category/Merchant'
    'src/Controller/Category'
    'src/Event/Category'
)

foreach ($rel in $paths) {
    $full = Join-Path $PSScriptRoot ('..\' + $rel.Replace('/', '\'))
    if (Test-Path $full) {
        Remove-Item $full -Recurse -Force
    }
}
