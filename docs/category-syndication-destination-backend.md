# Category syndication destination backend

This backend foundation introduces explicit destination registration for downstream category syndication.

## Scope of this wave

- destination identity
- destination type validation
- delivery mode validation
- normalized destination settings
- in-memory repository contract
- registration event payload for future delivery/history waves

## Supported destination types

- marketplace
- storefront
- search
- feed
- partner

## Supported delivery modes

- push
- pull
- export

## Why this exists

Category publishing maturity is not complete without destination awareness. This wave creates a clean Symfony-oriented backend contract for future steps such as mapping profiles, delivery ledgers, retries, and audit-grade publication history.
