# Category GraphQL + Store Readiness

This report tracks the remaining demo-drift removal for:

- `/api/category/store`
- `ReadOptimizer`
- `GraphqlResolver`
- `GraphqlFacetResolver`

Pass means:

- store reads are projection-backed rather than hardcoded arrays
- GraphQL category/facet reads are projection/search backed rather than in-memory or sqlite-memory fallbacks
- no file-based perf side effects remain in `ReadOptimizer`
