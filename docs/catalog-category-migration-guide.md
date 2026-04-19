# Catalog category migration guide

## Current recommendation

Use the existing `Category*` API contract as the public surface while incrementally adopting safer internal building blocks.

## Safe migration order

1. Introduce request normalization helpers.
2. Introduce request context resolver.
3. Normalize exception mapping with a subscriber.
4. Add mutation regression fixtures and replay coverage.
5. Only then evaluate route or class renames.
