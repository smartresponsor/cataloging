# Install Category RC1

1. php bin/console doctrine:migrations:migrate --no-interaction
2. psql -f config/sql/pg_ltree.sql
3. mysql < config/sql/mysql_infra_category.sql
