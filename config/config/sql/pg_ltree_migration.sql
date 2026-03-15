-- Enable ltree and convert path column to ltree type with GIST index
CREATE EXTENSION IF NOT EXISTS ltree;
ALTER TABLE category
  ALTER COLUMN path TYPE ltree USING text2ltree(path);
CREATE INDEX IF NOT EXISTS idx_category_path_gist ON category USING GIST (path);
