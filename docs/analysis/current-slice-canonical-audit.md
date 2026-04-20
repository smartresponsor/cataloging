# Current Slice Canonical Audit

## 1) canonical/compliant areas
- Namespace mapping App\Cataloging\ -> src/ is configured in composer autoload.
- Runtime target is PHP ^8.4.
- Doctrine ORM is on major 3 as required.
- No forbidden Catalog/Cataloging, Port, Adaptor, Infra, or opr paths found under src/ and tests/.

## 2) violations
- Symfony packages are on major 7.x (expected Symfony 8 by protocol).
- phpDocumentor package not declared.
- Nelmio API Bundle dependency is not declared.
- Non-root dot-folders exist in first-party tree (forbidden by protocol).
- Dot-folder instances: .commanding/.github, .commanding/logs/inspection/.tmp

## 3) required moves/renames/removals
- Move or remove `.commanding/.github` to a root-level allowed dot-folder or a non-dot directory.
- Move or remove `.commanding/logs/inspection/.tmp` because nested dot-folders are forbidden.

## 4) remaining legacy tails
- No explicit legacy-tail directories found in first-party source tree.

## 5) invalid Catalog/Cataloging placements
- No invalid Catalog/Cataloging placements detected under src/ and tests/.

## 6) convergence assessment toward a unified Symfony-oriented application
- Partial convergence: namespace/root/runtime and Doctrine are mostly aligned, but protocol-level drift remains (Symfony major, missing Nelmio declaration, and dot-folder policy violations).
