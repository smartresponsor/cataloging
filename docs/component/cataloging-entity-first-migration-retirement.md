# Cataloging entity-first migration retirement

## Scope

This patch moves Cataloging away from schema-first sources and keeps PHP entities as the durable model source.

## Retired schema-first sources

- `Cataloging/migrations/**`
- `Cataloging/config/sql/**`
- `Cataloging/tools/migration/**`

These paths described tables such as `category`, `category_audit`, `category_projection`, `category_slug_history`, `record_index`, `redirect_rule`, `seo_redirect`, `virtual_category`, and runtime hardening columns. Existing PHP entities already cover the main runtime model, so the SQL/PHP migration files are removed from the component slice.

## Restored concepts from old Entity/Category monolith

- `CategoryEnGb` / `CategoryEnUs` became `CatalogCategoryTranslationEntity`; locale-specific classes are not preserved.
- `CategoryFeatured` became `CatalogCategoryFeaturedEntity`.
- Product-category relation is represented as `CatalogCategoryProductBindingEntity` instead of leaking the old Product-side model into Cataloging.
- Attachment localization is represented as `CatalogCategoryAttachmentTranslationEntity` to keep attachment mechanics generic while Cataloging owns category-facing labels.

## Objecting alignment

New entities use Objecting embeddable traits for identity, audit, locale, and state fields:

- `ObjectIdentityEmbeddableTrait`
- `ObjectAuditEmbeddableTrait`
- `ObjectLocaleEmbeddableTrait`
- `ObjectStateEmbeddableTrait`

No local timestamp/soft-delete/system-field traits were added.

## Notes

Existing bound Cataloging entities were not renamed or duplicated. Flat `App\Cataloging\Entity\*` class aliases were added only for compatibility with current service bindings while canonical implementation lives under `App\Cataloging\Entity\Catalog`.
