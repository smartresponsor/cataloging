$paths = @(
    'src/Repository/CategoryRepository.php'
    'src/Security/CategoryVoter.php'
    'src/Observability/CategoryProjectionMetrics.php'
    'src/Command/CategorySeedCommand.php'
    'src/Command/CategorySlugSmokeCommand.php'
    'src/GraphQl/CategoryStateProvider.php'
)

foreach ($rel in $paths) {
    $full = Join-Path $PSScriptRoot ('..\' + $rel.Replace('/', '\'))
    if (Test-Path $full) {
        Remove-Item $full -Force
    }
}
