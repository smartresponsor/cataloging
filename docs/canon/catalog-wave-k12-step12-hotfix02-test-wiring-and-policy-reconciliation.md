# Catalog wave K12 Step12 hotfix02 — test wiring and policy reconciliation

Base slice: `cataloging-waveK12-step12-hotfix01-cumulative-snapshot.zip`

This hotfix reconciles the remaining PHPUnit red list after hotfix01:

- restores strict channel requirement in `CategoryMediaGovernancePolicy`
- updates fallback service test to use direct shared binding fixture instead of governance service
- fixes destination media preference test wiring to use `CategoryMediaApplicabilityService`
- fixes fallback-aware package gate test import/class wiring
