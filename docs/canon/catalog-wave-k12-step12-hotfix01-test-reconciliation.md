# Cataloging K12 Step12 Hotfix01 — test reconciliation

- Added missing named constructors on `CategoryChangeRequestState`.
- Allowed shared/global media bindings with empty `channels` / `locales`.
- Relaxed workflow transition policy for direct `draft -> approved` and no-op transitions used by review coupling.
- Fixed publication quality defaults so omitted optional checks do not become blockers/warnings.
- Hardened governed media coverage: inline media without governed required bindings now fails `requiredMediaCoverageReady`.
- Updated stale tests to current APIs for media role construction and syndication destination registration.
