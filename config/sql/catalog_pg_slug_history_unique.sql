-- Unique index to avoid duplicate slug history rows
CREATE UNIQUE INDEX IF NOT EXISTS uniq_category_slug_history_slug ON category_slug_history(slug);
