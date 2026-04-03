# Category GA checklist

## Hard gates

- working tree clean (`git status --short` returns empty)
- `APP_ENV=prod APP_DEBUG=0 php bin/console about` passes
- `php vendor/bin/phpstan analyse --no-progress --error-format=raw` passes
- bundle loadability is fully green in `report/inspection/catalog-dependency-baseline-report.json`
- runtime proof, route inventory, dependency baseline, owner overlap, class alias and RC readiness reports are freshly generated

## RC-conditioning gates

- `report/inspection/catalog-rc-readiness-report.json` is reviewed
- warning-class items are either resolved or explicitly accepted
- PHPUnit environment is complete enough to execute required suites
- smoke scripts required for the target environment were executed

## Release artifacts

- release manifest generated
- deployment runbook updated
- rollback path reviewed against current schema/runtime state
