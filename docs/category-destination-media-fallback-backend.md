# Category Destination Media Fallback Backend

## Goal

Add a backend-only policy layer that distinguishes **exact destination media coverage** from **shared fallback coverage**.

This supports common enterprise catalog scenarios where a destination can publish using:

- exact channel + locale media
- channel-shared media
- locale-shared media
- globally shared media

without forcing the component into a DAM or adapter-heavy architecture.

## What this wave adds

- `CategoryDestinationMediaFallbackPolicy`
- `CategoryDestinationMediaFallbackService`
- `CategoryDestinationMediaFallbackReport`
- fallback-aware evaluation event

## Resulting backend behavior

For destination-aware media evaluation, the backend now separates:

- `publishable` = exact destination coverage only
- `publishableWithFallback` = publishable when shared fallback assets satisfy required roles

## Main checks

- `exactDestinationMediaReady`
- `sharedChannelFallbackReady`
- `sharedLocaleFallbackReady`
- `globalSharedFallbackReady`
- `fallbackCoverageReady`
- `destinationMediaReadyWithFallback`
- `fallbackUsed`

## Why this matters

This brings the component closer to market-default enterprise behavior for category media governance:

- strict exact coverage can still be measured
- controlled fallback can still unblock destination publishing
- shared assets remain governed rather than implicit
