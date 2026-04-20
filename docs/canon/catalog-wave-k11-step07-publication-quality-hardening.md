# K11 Step07 — publication quality hardening

## Goal

Add a narrow backend layer that classifies publication quality into hard blockers, soft warnings, and advisory warnings.

## Added elements

- `src/ValueObject/CategoryPublicationQualityProfile.php`
- `src/ValueObjectInterface/CategoryPublicationQualityProfileInterface.php`
- `src/Policy/CategoryPublicationQualityPolicy.php`
- `src/PolicyInterface/CategoryPublicationQualityPolicyInterface.php`
- `src/Service/CategoryPublicationQualityService.php`
- `src/ServiceInterface/CategoryPublicationQualityServiceInterface.php`
- `src/Event/CategoryPublicationQualityEvaluated.php`
- `src/EventInterface/CategoryPublicationQualityEvaluatedInterface.php`

## Why this wave exists

The previous completeness/publication gates already said whether a category was publishable. This step adds sharper operator-grade semantics:

- what is a release-stopping blocker
- what is a non-blocking warning
- what is an advisory presentation gap

## Guardrails preserved

- single Symfony-oriented application root
- `App\Cataloging\ -> src/`
- no `Port` / `Adaptor` / hexagonal skeleton
- no parallel application tree
- no domain-root wrapper restoration
