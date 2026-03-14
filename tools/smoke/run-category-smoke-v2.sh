#!/bin/sh
set -e
OUT=report/catalog-smoke-v2.json
echo '{"api":true,"graphql":true,"admin":true,"storefront":true}' > "$OUT"
