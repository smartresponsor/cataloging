# Category syndication mapping backend

This backend capability introduces a narrow mapping-profile and publish-package foundation for category syndication.

## Scope

- mapping profile normalization
- destination-oriented field map definition
- required destination field declaration
- locale mode validation
- publish package build for downstream delivery

## Backend intent

This step does not introduce transport delivery, retries, or destination history.
It only prepares a normalized payload package that later syndication delivery waves can consume.

## Normalized locale modes

- `per_locale`
- `shared`
- `destination_default`

## Outputs

A publish-package event now emits:

- `packageId`
- `destinationId`
- `categoryId`
- `version`
- `localeMode`
- `payload`
- `missingRequiredFields`
- `publishable`
- `fieldMap`
- `requiredFields`
- `actorId`
- `reason`
