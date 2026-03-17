# Wave 15 — adapter/runtime truth

## What changed
- Added `CategoryRepositoryStateStore` as a file-backed adapter seam for repository state.
- `CategoryRepository` can now export/import deterministic state.
- `PublishCategoryCommand` can load persisted state before mutation and save it after mutation.

## Why it matters
This wave reduces reliance on a single in-memory object lifetime. It proves that a runtime path can:
1. restore category state,
2. perform mutation,
3. emit delivery side effects,
4. persist the updated state for the next process.

## Net effect
This is still not full database-backed realism, but it is a more adult adapter-backed runtime seam than pure same-process mutation tests.
