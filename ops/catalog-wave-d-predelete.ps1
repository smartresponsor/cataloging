$paths = @(
    'tests',
    'tools/linter/category_mirror_check.php',
    'tools/linter/category_prefix_check.php',
    'tools/Category'
)

foreach ($rel in $paths) {
    $full = Join-Path $PSScriptRoot ('..\' + $rel.Replace('/', '\'))
    if (Test-Path $full) {
        Remove-Item $full -Recurse -Force
    }
}
