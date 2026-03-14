# Cataloging wave 3 semantic collapse

Wave 3 collapses the most obvious remaining semantic duplicates where the canonical owner is already clear.

Applied:
- `App\Event\Category\CategoryMoved` collapsed into root `App\Event\CategoryMoved`
- `App\GraphQl\Category\CategoryQuery` removed in favor of root `App\GraphQl\CategoryQuery`
- `App\Projection\Category\CategoryProjectionRunner` removed in favor of root `App\Projection\CatalogProjectionRunner`
- obsolete wrapper `App\Security\Category\CategoryVoter` removed in favor of root `App\Security\CategoryVoter`

Why this is safe:
- internal references already favor the root owners or can be rewritten without ambiguity
- local proof already validated the root voter and root query lines
- the runner compatibility test can target the root canonical projection runner
