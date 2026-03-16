# tests import/export

Export:
  php bin/console category:export > category.ndjson

Import:
  php bin/console category:import category.ndjson
