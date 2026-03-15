$ErrorActionPreference = 'Stop'
$targets = @(
  'src/Service/Category',
  'src/ServiceInterface/Category'
)
foreach ($target in $targets) {
  if (Test-Path $target) {
    Remove-Item $target -Recurse -Force
  }
}
Write-Host 'Catalog Wave A pre-delete complete.'
