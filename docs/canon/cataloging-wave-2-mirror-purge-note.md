# Cataloging wave 2 mirror purge

Wave 2 from snapshot `132` focuses on the safest remaining mirror/bridge debt.

Applied:
- collapse `App\Repository\Category\CategoryRepository` into `App\Repository\CategoryRepository`
- collapse `App\GraphQl\Category\CategoryStateProvider` into `App\GraphQl\CategoryStateProvider`
- remove no-op self-alias tails from selected controller and GraphQl files

Why this shape:
- after Wave 1, pure interface wrapper debt was already much smaller than expected
- the highest-value safe reduction was remaining root-wrapper mirrors and self-alias noise
- semantic-conflict files are still deferred to the next wave
