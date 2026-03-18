# Category destination media policy preferences backend

K12 Step08 introduces destination-level media policy preferences.

## Goal

Different syndication destinations can now express whether they:

- require strict exact media coverage,
- allow fallback-backed publishability,
- or prefer exact coverage but still allow fallback with warnings.

## Supported media policy modes

- `strict_exact`
- `allow_fallback`
- `prefer_exact_warn`

## Result payload

The evaluation payload includes:

- `mediaPolicyMode`
- `strictPublishable`
- `fallbackPublishable`
- `resolvedPublishable`
- `fallbackUsed`
- `requiredMissing`
- `warnings`
- `checks`

## Architectural note

This step does not rewrite existing publish or attachment flows.
It adds a narrow policy-preference layer on top of the already introduced:

- destination media readiness
- destination media fallback
- syndication package gating foundations
