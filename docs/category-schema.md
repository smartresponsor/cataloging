# tests schema

- table: category
- columns: id (uuid), slug, name, parent_id, level, path, locale
- indexes: slug unique, (parent_id, slug)
