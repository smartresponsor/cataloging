#!/bin/sh
set -e
OUT=report/catalog-release-notes.md
echo '# Category RC1 Release Notes' > "$OUT"
for p in m n o p q r s t; do
  FILE="/mnt/data/category-rc1-$p-pack.zip"
  if [ -f "$FILE" ]; then
    echo "- included: $p" >> "$OUT"
  else
    echo "- missing: $p" >> "$OUT"
  fi
done
echo '{"version":"rc1","packs":["m","n","o","p","q","r","s","t"]}' > report/catalog-release-index.json
