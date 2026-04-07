# Category GraphQL boundary

GraphQL in Cataloging is a **secondary read adapter / compatibility surface**.

It is not the primary domain boundary and it is not the strategic write surface.

## Current role

GraphQL exists to:
- expose projection-backed category reads;
- provide compatibility and convenience for read-centric consumers;
- reuse the same underlying read/search model as HTTP read surfaces.

## Non-goals

GraphQL must not become:
- the single source of truth for category business rules;
- a parallel write orchestration layer;
- a reason to move all delivery surfaces into GraphQL-first design.

## Boundary rule

GraphQL resolvers should stay thin and delegate to shared read/search services.
If GraphQL remains in the repository, it should stay documented as a compatibility/read adapter rather than as a primary bounded context boundary.
