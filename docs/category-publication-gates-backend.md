# Category publication gates backend

K11 step02 adds backend publication-gate evaluation on top of the workflow foundation introduced in K11 step01.

## Purpose

Publishing now has an explicit gate evaluation layer before any final publish transition is allowed.

The gate expects:

- workflow state = `approved`
- `slugReady = true`
- `seoReady = true`
- `contentReady = true`
- `localeReady = true`

Optional-but-tracked warnings:

- `mediaReady`
- `aliasReady`

## Backend contracts

- `App\Cataloging\ValueObject\CategoryPublicationReadiness`
- `App\Cataloging\Policy\CategoryPublicationGatePolicy`
- `App\Cataloging\Service\CategoryPublicationGateService`
- `App\Cataloging\Event\CategoryPublicationGateEvaluated`

## Current scope

This step does **not** rewrite legacy publish endpoints or legacy publish operations.
It establishes canonical backend contracts that later waves can plug into admin/API/integration flows.

## Result model

Evaluation returns:

- category id
- workflow state
- publishable yes/no
- blockers list
- warnings list
- normalized checks
- actor id
- reason
- occurred at timestamp

## Canonical intent

This keeps publication control inside current Symfony-oriented application layers and avoids any Port/Adaptor or hexagonal expansion.
