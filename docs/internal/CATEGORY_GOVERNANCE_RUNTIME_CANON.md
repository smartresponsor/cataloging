# Category Governance Runtime Canon

- Category remains the root aggregate.
- User remains an external actor, not the default category owner model.
- CategoryAccessAssignment is the object-level governance bridge.
- CategoryGovernanceView is a read-side surface for admin/runtime inspection.
- CategoryReviewAssignment stays workflow-specific and does not replace general access assignment.
- No direct ORM relation to external User is allowed.
