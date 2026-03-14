# Cataloging boot-proof note

Scope of this wave:
- add canonical `config/services.yaml`
- add attribute route import
- fix admin move route to the canonical controller
- align phpunit suite to `tests/Category`
- replace placeholder-level tests with structural behavior checks
- add smoke scripts for runtime and fixture parity

Recommended local run order:
1. `composer validate`
2. `composer install`
3. `composer lint`
4. `composer smoke:runtime`
5. `composer smoke:fixtures`
6. `composer test`
