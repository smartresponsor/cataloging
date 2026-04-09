# Category Actor Traceability Canon

- `Category` remains the root aggregate.
- Actor fields like `submittedBy`, `reviewedBy`, `assignedBy`, `assignedReviewer`, and `actorId` are traceability references.
- These fields do not redefine category ownership.
- Object-level governance is handled by `CategoryAccessAssignment`.
- Workflow and review seams remain domain-specific children of category governance.
- No direct ORM relation to external `User` is required for traceability.
