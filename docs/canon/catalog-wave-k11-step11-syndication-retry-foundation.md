# Catalog Wave K11 Step11 — Syndication Retry Foundation

Base slice: K11 step10 cumulative snapshot.

This wave adds a Symfony-oriented retry and recovery foundation for failed category syndication deliveries.

## Added contracts

- CategorySyndicationRetryPlan
- CategorySyndicationRecoveryCandidate
- CategorySyndicationRetryPolicy
- CategorySyndicationRetryService
- recovery and retry events/interfaces

## Notes

- no Port/Adaptor layer introduced
- no parallel application tree introduced
- no legacy publish stack rewrite
