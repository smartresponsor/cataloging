$paths = @(
    'tools/canary/run-category-canary.sh'
)

foreach ($rel in $paths) {
    $full = Join-Path $PSScriptRoot ('..\' + $rel.Replace('/', '\'))
    if (Test-Path $full) {
        Remove-Item $full -Force
    }
}
