#!/bin/sh
set -eu

php -v

if [ ! -f vendor/bin/phpunit ]; then
  echo 'phpunit not installed, skipping'
  exit 0
fi

vendor/bin/phpunit

if [ -f build/logs/clover.xml ] && [ -f tools/qa/check_coverage_threshold.php ]; then
  php tools/qa/check_coverage_threshold.php build/logs/clover.xml 70 || exit 1
fi
