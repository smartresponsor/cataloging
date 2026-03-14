# Cataloging runtime-proof pack

Added in this pack:
- `smoke:container`
- `smoke:doctrine`
- `smoke:fixture-load`
- `smoke:graphql`
- `report:runtime-proof`

Recommended local run order:
1. `composer validate`
2. `composer install`
3. `composer smoke:container`
4. `composer smoke:doctrine`
5. `composer smoke:fixture-load`
6. `composer smoke:graphql`
7. `composer test`
8. `composer report:runtime-proof`
