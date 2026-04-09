# Category CLI parity step02

This step extends operator CLI coverage with commands for review assignment and for evaluation of completeness and publication quality.

## Commands

- `category:review:assign <requestId> <reviewer> <assignedBy> [--priority=normal] [--due-at=DATE_ATOM]`
- `category:completeness:evaluate <categoryId> <actorId> <reason> --payload='{"...": ...}'`
- `category:quality:evaluate <categoryId> <score> <actorId> <reason> --publication-checks='{"...": ...}' [--checks='{"...": ...}']`

## Intent

The goal is to expose core governance evaluation surfaces through Symfony Console without introducing parallel operator frameworks.
