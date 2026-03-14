#!/bin/sh
set -e
DLQ=report/catalog-import-dlq.json
echo '[]' > "$DLQ"
while read -r line; do
  if echo "$line" | grep -q 'ERROR'; then
    python - <<'PY'
import json,sys
dlq=json.load(open('report/catalog-import-dlq.json'))
dlq.append({'line':sys.argv[1]})
json.dump(dlq, open('report/catalog-import-dlq.json','w'))
PY
  fi
done < "${1:-/dev/stdin}"
