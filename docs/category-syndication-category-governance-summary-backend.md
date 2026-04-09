# Category Syndication Category Governance Summary Backend

K12 Step12 adds a category-level operational summary over governance trail payloads.

## Purpose

This backend capability aggregates governance trail records across multiple destinations for a single category.

## Summary fields

- total trails
- resolved publishable count
- fallback used count
- retryable count
- retry scheduled count
- failure trail count
- delivered trail count
- destination ids
- status counts
- media policy mode counts
- warning codes

## Operational checks

- `categoryGovernanceSummaryHasTrails`
- `categoryGovernanceSummaryHasDestinations`
- `categoryGovernanceSummaryHasResolvedPublishable`
- `categoryGovernanceSummaryHasFallbackUsage`
- `categoryGovernanceSummaryHasFailures`
- `categoryGovernanceSummaryHasDelivered`
- `categoryGovernanceSummaryHasRetryScheduled`

## Notes

This is an operational reporting profile only. It does not replace delivery history, retry planning, or destination-level summaries.
