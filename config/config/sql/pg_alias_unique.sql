-- Unique index to avoid duplicate alias rows
CREATE UNIQUE INDEX IF NOT EXISTS uniq_category_alias ON category_alias(old_slug);
