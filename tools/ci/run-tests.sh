#!/bin/sh
set -eu

php -v

if [ ! -x vendor/bin/phpunit ]; then
  echo 'phpunit not installed: expected executable at vendor/bin/phpunit' >&2
  exit 1
fi

coverage_report_path="${COVERAGE_REPORT_PATH:-build/logs/clover.xml}"
coverage_threshold="${COVERAGE_THRESHOLD:-70}"

mkdir -p build/logs
php tools/php/php84.php vendor/bin/phpunit -c phpunit.xml.dist --coverage-clover "$coverage_report_path"

if [ ! -s "$coverage_report_path" ]; then
  echo "coverage report was not generated: $coverage_report_path" >&2
  exit 1
fi

php tools/php/php84.php tools/qa/check_coverage_threshold.php "$coverage_report_path" "$coverage_threshold"
