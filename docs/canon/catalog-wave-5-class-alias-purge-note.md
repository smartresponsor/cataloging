# Cataloging wave 5 class-alias purge

Wave 5 removes the last explicit `class_alias()` compatibility tails from the live source tree.

Applied:
- removed 9 `class_alias()` tails from controller, controller-interface, GraphQl, and Api/Graphql files
- kept the actual canonical classes intact
- did not change runtime logic of the surviving classes

Effect:
- compatibility alias layer is no longer embedded in the live runtime files
- the next remaining debt is almost entirely semantic duplicate ownership, not alias mechanics
