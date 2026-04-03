CREATE TABLE IF NOT EXISTS category_projection (
  id varchar(36) primary key,
  slug varchar(255) not null,
  name varchar(255) not null,
  parent_id varchar(36) null,
  path varchar(500) not null,
  locale varchar(12) not null,
  tenant varchar(64) default 'default',
  workflow_state varchar(32) not null default 'draft',
  published tinyint(1) not null default 0,
  published_at datetime null,
  updated_at datetime not null,
  key idx_category_projection_parent (parent_id),
  key idx_category_projection_locale (locale),
  key idx_category_projection_tenant (tenant),
  key idx_category_projection_published (published)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
