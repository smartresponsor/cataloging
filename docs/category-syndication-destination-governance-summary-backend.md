# Category syndication destination governance summary backend

K12 step11 introduces a destination-level governance summary layer for category syndication.

## Purpose

This layer aggregates governance trail payloads into a normalized operational summary per destination.
It helps answer:

- how many governance trail records exist for a destination
- how often fallback was used
- how many trails were resolved publishable
- how many trails involved retry scheduling or failures
- which media policy modes appear most often
- which warning codes recur for this destination

## Summary payload

The summary payload includes:

- destinationId
- totalTrails
- resolvedPublishableCount
- fallbackUsedCount
- retryableCount
- retryScheduledCount
- failureTrailCount
- deliveredTrailCount
- statusCounts
- policyModeCounts
- warningCodes
- checks

## Architectural note

This step does not rewrite the legacy publish stack.
It stays in the canonical Symfony-oriented layout and extends the existing syndication governance trail introduced in K12 step10.
