# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
# Rename Cataloging-local CategoryHtmlBlock entity to a component-scoped class.
# Run from the Cataloging repository root.

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

$root = (Get-Location).Path
$timestamp = Get-Date -Format 'yyyyMMdd-HHmmss'
$backupRoot = Join-Path $root "var/patch-backup/cataloging-category-html-block-entity-rename-$timestamp"

function Backup-File([string] $path) {
    if (-not (Test-Path $path)) {
        return
    }

    $resolved = Resolve-Path $path
    $relative = $resolved.Path.Substring($root.Length).TrimStart('\', '/')
    $target = Join-Path $backupRoot $relative

    New-Item -ItemType Directory -Force -Path (Split-Path $target -Parent) | Out-Null
    Copy-Item -Path $path -Destination $target -Force
}

function Replace-In-File([string] $path, [string] $pattern, [string] $replacement) {
    if (-not (Test-Path $path)) {
        return
    }

    $content = Get-Content $path -Raw
    $updated = $content -replace $pattern, $replacement

    if ($updated -ne $content) {
        Backup-File $path
        Set-Content -Path $path -Value $updated -NoNewline
    }
}

$oldFile = Join-Path $root 'src/Entity/CategoryHtmlBlock.php'
$newFile = Join-Path $root 'src/Entity/CatalogCategoryHtmlBlock.php'

if (Test-Path $oldFile) {
    Backup-File $oldFile

    New-Item -ItemType Directory -Force -Path (Split-Path $newFile -Parent) | Out-Null
    $content = Get-Content $oldFile -Raw

    $content = $content -replace '\bclass\s+CategoryHtmlBlock\b', 'class CatalogCategoryHtmlBlock'

    # Scope explicit generic Doctrine table names when present.
    $content = $content -replace "name:\s*'category_html_block'", "name: 'catalog_category_html_block'"
    $content = $content -replace 'name:\s*"category_html_block"', 'name: "catalog_category_html_block"'
    $content = $content -replace "name:\s*'category_html_blocks'", "name: 'catalog_category_html_blocks'"
    $content = $content -replace 'name:\s*"category_html_blocks"', 'name: "catalog_category_html_blocks"'

    Set-Content -Path $newFile -Value $content -NoNewline
    Remove-Item $oldFile -Force
}

$src = Join-Path $root 'src'
if (Test-Path $src) {
    $phpFiles = Get-ChildItem -Path $src -Recurse -Filter '*.php' -File

    foreach ($file in $phpFiles) {
        $path = $file.FullName

        Replace-In-File $path 'App\Cataloging\\Entity\\CategoryHtmlBlock' 'App\Cataloging\Entity\CatalogCategoryHtmlBlock'
        Replace-In-File $path 'use\s+App\Cataloging\\Entity\\CategoryHtmlBlock;' 'use App\Cataloging\Entity\CatalogCategoryHtmlBlock;'
        Replace-In-File $path '\bCategoryHtmlBlock::class\b' 'CatalogCategoryHtmlBlock::class'
        Replace-In-File $path '\bnew\s+CategoryHtmlBlock\s*\(' 'new CatalogCategoryHtmlBlock('
        Replace-In-File $path '\bCategoryHtmlBlock\s+\$categoryHtmlBlock\b' 'CatalogCategoryHtmlBlock $categoryHtmlBlock'
        Replace-In-File $path '\?CategoryHtmlBlock\s+\$categoryHtmlBlock\b' '?CatalogCategoryHtmlBlock $categoryHtmlBlock'
        Replace-In-File $path '\biterable<CategoryHtmlBlock>' 'iterable<CatalogCategoryHtmlBlock>'
        Replace-In-File $path '\barray<CategoryHtmlBlock>' 'array<CatalogCategoryHtmlBlock>'
    }
}

if (Test-Path $newFile) {
    php -l $newFile
}

Write-Host 'Cataloging CategoryHtmlBlock entity rename completed.'
Write-Host 'Old: src/Entity/CategoryHtmlBlock.php'
Write-Host 'New: src/Entity/CatalogCategoryHtmlBlock.php'
Write-Host "Backup: $backupRoot"
