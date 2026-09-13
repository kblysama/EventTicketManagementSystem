#!/bin/sh
set -e

cd /var/www/html

export COMPOSER_HOME="${COMPOSER_HOME:-/tmp/composer}"
export COMPOSER_CACHE_DIR="${COMPOSER_CACHE_DIR:-/tmp/composer-cache}"
mkdir -p "$COMPOSER_HOME" "$COMPOSER_CACHE_DIR"

if [ ! -f .env ]; then
    cp .env.example .env
fi

if [ "${RUN_SETUP:-0}" = "1" ]; then
    composer install --no-interaction --prefer-dist --no-progress

    if ! grep -q "APP_KEY=base64:" .env; then
        php artisan key:generate --force
    fi

    if [ ! -d node_modules ] || [ ! -f public/build/manifest.json ]; then
        npm install --no-fund --no-audit
        npm run build
    fi

    php artisan migrate --force
    php artisan db:seed --force
    php artisan storage:link || true
else
    i=0
    while [ ! -f vendor/autoload.php ]; do
        i=$((i + 1))
        if [ "$i" -gt 90 ]; then
            echo "vendor/autoload.php still missing after waiting"
            exit 1
        fi
        echo "waiting for app setup..."
        sleep 2
    done
fi

exec "$@"
