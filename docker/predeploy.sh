#!/bin/sh
set -eu

cd /app
php bin/console app:duckdb:check
php bin/console secrets:decrypt-to-local --force
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
