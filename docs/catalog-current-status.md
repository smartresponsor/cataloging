# Cataloging current status

Cataloging is an RC working line for catalog and category discovery responsibility.

## Current baseline

- Runtime target: PHP 8.4 and Symfony 8.
- Composer package: `cataloging/catalog`.
- Bundle class: `App\\Cataloging\\CatalogingBundle`.
- Main namespace: `App\\Cataloging\\`.
- Test namespace: `App\\Cataloging\\Tests\\`.
- Component status: `rc`.

## Current RC direction

Cataloging converges toward a Symfony-native component with:

- explicit service, route, Doctrine, Messenger, API Platform, and security export files;
- no wildcard CRUD controller discovery;
- no generic CRUD route ownership;
- entity-first persistence modeling inside Cataloging operations;
- DTO/value-object/event contracts for external and async seams;
- explicit readiness reports under `tools/inspection/` and `report/inspection/`.

## Transitional vocabulary

Older local documents and compatibility contracts may still mention Category as an earlier repository name or API compatibility surface. New RC-facing documentation should use Cataloging for the component and category/catalog for owned business concepts.

## Current proof commands

- `composer validate`
- `composer test`
- `composer report:route-inventory`
- `composer report:boundary-readiness`
