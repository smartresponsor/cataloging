# Catalog proof runbook

Run these from the repository root.

## Validation
- `composer validate`

## Tests
- `composer test`
- `composer test:unit`
- `composer test:integration`
- `composer test:e2e`

## Static / style
- `composer lint`
- `composer cs:check`
- `composer md`
- `composer md:tests`

## Smoke
- `composer smoke:runtime`
- `composer smoke:fixtures`
- `composer smoke:container`
- `composer smoke:doctrine`
- `composer smoke:fixture-load`
- `composer smoke:graphql`

## Reports
- `composer report:class-alias`
- `composer report:owner-overlap`
- `composer report:route-inventory`
- `composer report:runtime-proof`
