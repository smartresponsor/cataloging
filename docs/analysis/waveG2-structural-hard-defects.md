# Wave G2 — structural hard defects

Applied against the latest current slice as the only base.

## Closed in G2
- removed duplicated config/config subtree artifacts where canonical copies already exist under config/
- removed nested public/public/index.php entrypoint
- repaired PSR-4/file-integrity defects in CategoryMoveController, CategoryMoved, CategoryPathRebased
- repaired broken Rule classes (CategoryRule, RuleEvaluator)
- normalized GraphQl namespace usage to App\Cataloging\GraphQl for files physically under src/GraphQl
- aligned Category service namespaces with their physical paths
- removed report backup artifacts from the repository snapshot

## Notes
- This wave is structural and does not claim full runtime validation.
- Remaining competing-code and synthetic seam zones are intentionally deferred to later waves.
