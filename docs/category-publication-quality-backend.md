# Category publication quality backend

## Purpose

This backend layer hardens publication readiness by separating:

- hard blockers that must stop publication-quality approval
- soft warnings that should be visible to operators
- advisory warnings for presentation completeness that can be resolved later

It intentionally extends the existing completeness/publication-gate foundation without replacing the current publish stack.

## Inputs

The service consumes:

- normalized publication checks
- full completeness checks
- completeness score
- actor and reason for auditability

## Classification

### Hard blockers

The following checks are treated as publication-quality blockers:

- `slugReady`
- `seoReady`
- `contentReady`
- `localeReady`
- `qualityScoreCritical` when the score drops below the critical threshold

### Soft warnings

The following signals stay non-blocking but visible:

- `mediaReady`
- `aliasReady`
- `qualityScoreBelowTarget`

### Advisory warnings

The following signals remain advisory presentation gaps:

- `bannerReady`
- `htmlBlockReady`

## Output payload

The evaluation event returns:

- `publishableQuality`
- `riskLevel`
- `hardBlockers`
- `softWarnings`
- `advisoryWarnings`
- `publicationChecks`
- `checks`
- `actorId`
- `reason`

## Architectural note

This wave preserves the current Symfony-oriented structure and does not introduce generic workflow engines, port/adaptor layers, or parallel API styles.
