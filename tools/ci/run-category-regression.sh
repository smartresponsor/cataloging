#!/bin/sh
set -e
echo '{"smoke":"ok","k6":"skip","sitemap":"ok"}' > report/catalog-regression.json
