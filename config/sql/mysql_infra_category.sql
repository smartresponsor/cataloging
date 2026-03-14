CREATE TABLE IF NOT EXISTS category_projection (
  id varchar(36) primary key,
  slug varchar(255) not null,
  parent_id varchar(36) null,
  locale varchar(8) not null,
  tenant varchar(64) default 'default'
);
