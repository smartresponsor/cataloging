# Cataloging wave K11 step04 — review assignment and queue contour

## Goal

Add reviewer assignment and review queue/readiness backend contracts without introducing a generic workflow engine or a second application tree.

## Added canonical elements

- `src/Entity/CategoryReviewAssignment.php`
- `src/EntityInterface/CategoryReviewAssignmentInterface.php`
- `src/Policy/CategoryReviewAssignmentPolicy.php`
- `src/PolicyInterface/CategoryReviewAssignmentPolicyInterface.php`
- `src/Repository/CategoryReviewAssignmentRepository.php`
- `src/RepositoryInterface/CategoryReviewAssignmentRepositoryInterface.php`
- `src/Service/CategoryReviewAssignmentService.php`
- `src/ServiceInterface/CategoryReviewAssignmentServiceInterface.php`
- `src/Service/CategoryReviewQueueService.php`
- `src/ServiceInterface/CategoryReviewQueueServiceInterface.php`
- `src/ValueObject/CategoryReviewQueueItem.php`
- `src/ValueObjectInterface/CategoryReviewQueueItemInterface.php`
- `src/Event/CategoryChangeRequestAssigned.php`
- `src/EventInterface/CategoryChangeRequestAssignedInterface.php`

## Why this is canonical

The wave extends existing Symfony-oriented layer roots only.
It does not introduce `Domain`, `Port`, `Adaptor`, `Infra`, `Catalog`, or `Cataloging` wrapper trees.

## Business effect

The component now supports:

- reviewer assignment
- queue prioritization
- review readiness computation
- backend-only moderation visibility for future UI components
