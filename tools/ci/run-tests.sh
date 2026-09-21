#!/bin/sh
set -eu

php -v

if [ ! -f vendor/bin/phpunit ]; then
  echo 'phpunit not installed, skipping'
  exit 0
fi

vendor/bin/phpunit

