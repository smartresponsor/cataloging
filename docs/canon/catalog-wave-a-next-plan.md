# Catalog next execution plan

## Wave A
Evacuate or re-home legacy root layers:
- `src/Domain/**`
- `src/DomainInterface/**`
- `src/Infra/**`
- `src/InfraInterface/**`
- `src/Adapter/**`
- `src/Http/**`

## Wave B
Promote top-level component surface to `Catalog*`.

## Wave C
Promote inner-unit service surface to `CatalogCategory*`.

## Wave D
Flatten tests and align tools/docs.

## Wave E
Reset active docs/report truth layer.
