# Cataloging owner-collapse wave G

Applied in this wave:
- `App\Repository\CatalogRepository` becomes a compatibility alias to the canonical owner `App\Repository\CatalogRepository`
- `App\GraphQl\CategoryStateProvider` stays as a compatibility alias to `App\GraphQl\CategoryStateProvider`

Why:
- keep older imports alive
- make canonical owners explicit
- reduce duplicate-owner ambiguity for the next cleanup wave

Still intentionally unresolved in this wave:
- `CategoryResolver`
- `CategoryVoter`
- `CategoryProjectionRunner`
- `CacheInvalidator`

Those need semantic consolidation, not only path cleanup.
