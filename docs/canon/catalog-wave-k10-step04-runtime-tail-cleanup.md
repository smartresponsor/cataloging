# Catalog wave K10 step04 — runtime tail cleanup

This step removes residual non-canonical tails left after earlier wrapper flattening waves.

Changes:
- fix `src/Service/CategoryService.php` legacy import from removed service-internal wrapper class to `App\Entity\Category`
- rename non-canonical test abbreviation to the full `Infrastructure` segment
- update `.github/prompts/prompt-audit.yml` dependency direction keys from non-canonical structural vocabulary to Symfony-oriented `Entity` / `Controller` / `Infrastructure` vocabulary
- delete obsolete wave-g1 migration artifacts that referenced removed wrapper trees
