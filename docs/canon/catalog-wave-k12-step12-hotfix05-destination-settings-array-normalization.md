# K12 Step12 Hotfix05 — destination settings array normalization

Preserve array-valued destination settings such as `requiredMediaRoles` during destination registration normalization. This keeps fallback and policy-aware media gating aligned with required-role expectations instead of collapsing arrays into scalar strings.
