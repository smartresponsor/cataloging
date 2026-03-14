# Cataloging canon roadmap 001

Current target: move the repository from late-RC feature breadth to canon-stable RC.

## Wave order

1. Stop-the-bleeding
   - restore PHP syntax validity
   - restore linter script validity
   - inventory delete and move candidates
2. Canon structure cleanup
   - remove forbidden roots: src/Domain, src/DomainInterface, src/Adapter
   - collapse Infra into Infrastructure
   - eliminate config/config duplication
3. Owner consolidation
   - choose one canonical owner for GraphQl/Graphql, SEO, redirect, webhook, OIDC, repository duplicates
4. CI truthfulness
   - fail on syntax and missing tooling
   - align phpunit suites with actual tests tree
5. Release truth
   - unify README, manifest, milestone and release line

## Exit criteria for canon-stable RC

- no broken PHP files
- no forbidden canon roots left in runtime code
- no duplicate config roots
- namespace matches path for touched files
- one canonical owner per capability
- CI fails loudly instead of silently skipping core checks
