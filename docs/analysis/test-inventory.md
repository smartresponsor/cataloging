# Cataloging test inventory (interim)

## Real behavior tests
- `tests/Category/TenantFilterTest.php`
- `tests/Category/TreeOperationTest.php`
- `tests/Category/GraphQL/CategoryQueryAdvancedTest.php`
- `tests/Category/CategoryVoterTest.php`
- `tests/Category/Infra/CategoryAuditLoggerTest.php`
- `tests/Category/CategoryCacheServiceTest.php`

## Contract / presence tests
- `tests/Category/Api/CategoryContractTest.php`

## Smoke / weak seam tests
- `tests/Category/Infra/WebhookPublisherTest.php`
- `tests/Category/Api/CategoryTreeBrokenTest.php`

## Placeholder / decorative tests
- `tests/Category/Api/CategoryApiTest.php`
- `tests/Category/Api/CategoryAuthTest.php`
- `tests/Category/E2E/CreateMovePublishTest.php`
- `tests/Category/GraphQL/CategoryGraphQLTest.php`
- `tests/Category/Regression/CategoryRegressionTest.php`
- `tests/Category/WebhookClientTest.php`

## Summary
The test pack is green-capable but still narrow relative to repository surface. Core flows are not yet proven end-to-end.
