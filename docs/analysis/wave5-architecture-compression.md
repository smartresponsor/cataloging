# Wave 5 — architecture compression

## Objective
Reduce duplicate logic and tighten Symfony-oriented boundaries without expanding product surface.

## Changes in this wave

- collapsed duplicated API utility logic into thin wrappers around canonical `App\Service` implementations
- fixed several interface binding seams so service classes point to explicit `*Interface` contracts
- normalized tenant-role policy to shared `CatalogCategoryRole` constants
- neutralized legacy duplicate class residue in `CanonicalPolicyLocale-.php`

## Result

W5 does not materially expand behavior. It reduces ambiguity and lowers the chance of hidden runtime fatals caused by namespace drift or duplicate logic forks.

## Remaining W6/W7 style pressure

- broader contract verification across controller / command / import-export surfaces
- repository-backed runtime truth with real persistence adapter
- observability/runbook realism checks against actual code paths
