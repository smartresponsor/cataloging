# Install Category RC1

1. php bin/console doctrine:migrations:migrate --no-interaction
2. psql -f config/sql/catalog_pg_ltree.sql
3. mysql < config/sql/catalog_mysql_infra_category.sql
