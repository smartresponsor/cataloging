# Category Media Applicability Backend

K12 Step03 adds channel-specific and locale-specific media applicability evaluation on top of governed media bindings.

## Purpose

The goal is to answer whether existing category media bindings are suitable for a concrete downstream publication context:

- channel-specific publication target
- locale-specific publication target
- required media roles for that target

## Scope

This step introduces:

- `CategoryMediaApplicabilityReport`
- `CategoryMediaApplicabilityPolicy`
- `CategoryMediaApplicabilityService`
- `CategoryMediaApplicabilityEvaluated`

## Input model

The evaluation payload may contain:

- `channel`
- `locale`
- `requiredRoles`

## Output model

The evaluation returns:

- `checks`
- `requiredMissing`
- `warnings`
- `matchedBindingIds`

## Checks

- `channelScopedMediaReady`
- `localeScopedMediaReady`
- `requiredRoleCoverageReady`
- `exactChannelLocaleMatchReady`

## Design note

This step does not replace existing attachment or publication flows. It adds a governed applicability layer that later waves can bridge into destination-specific publication readiness.
