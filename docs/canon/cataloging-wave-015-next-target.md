# Cataloging wave 015 next target

Remaining high-value canon cleanup targets after waves 011-014:

1. Evacuate the remaining `src/Domain/*` tree into canonical Symfony-oriented layers.
2. Normalize namespace-to-path mismatches under `App\...`.
3. Replace `config/config/*` shadow tree with hard deletion once reference scan is clean.
4. Convert garbage-suffix files (`*-`, `f..p`) into inspector-visible deletion candidates.
5. Resolve duplicate owners for GraphQl/Seo/Webhook/Security classes.
