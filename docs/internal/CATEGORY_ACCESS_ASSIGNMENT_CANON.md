# Category Access Assignment Canon

- `Category` is the root aggregate.
- `User` is an external actor, not the default ownership root.
- Access to category is governed through `CategoryAccessAssignment`.
- Traceability fields like `createdBy`, `assignedBy`, `reviewedBy` are actor references and do not define category ownership.
- `CategoryReviewAssignment` remains workflow-specific and does not replace general access assignment.
- No direct ORM relation to an external `User` aggregate is allowed.
