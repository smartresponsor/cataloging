-- MySQL infra schema (example)
CREATE TABLE IF NOT EXISTS record_index (
  id varchar(64) PRIMARY KEY,
  brand varchar(80),
  price decimal(12,2),
  stock int,
  tag_set json,
  KEY idx_brand (brand),
  KEY idx_price (price),
  KEY idx_stock (stock),
  CHECK (json_valid(tag_set))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS virtual_category_member (
  virtual_category_id varchar(26) NOT NULL,
  record_id varchar(64) NOT NULL,
  PRIMARY KEY (virtual_category_id, record_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
