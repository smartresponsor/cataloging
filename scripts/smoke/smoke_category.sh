#!/usr/bin/env bash
set -euo pipefail
BASE_URL="${1:-http://localhost:8080}"
echo "Smoke tests at $BASE_URL"
# Extend with real API calls when endpoints are wired.
exit 0
