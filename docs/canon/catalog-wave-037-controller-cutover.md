cataloging wave 037

Cut over root category controllers to the canonical `App\Controller\Category` namespace.

Compatibility policy:
- keep old `App\Controller\*` FQCNs alive through `class_alias`
- do not move file paths yet
- keep routes unchanged
