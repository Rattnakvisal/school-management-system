#!/bin/sh
set -e

cd /var/www/html

LOCK_HASH_FILE="vendor/.composer.lock.hash"
MANIFEST_HASH_FILE="vendor/.composer.manifest.hash"
CURRENT_LOCK_HASH=""
CURRENT_MANIFEST_HASH=""
STORED_LOCK_HASH=""
STORED_MANIFEST_HASH=""
NEEDS_COMPOSER_INSTALL=0

hash_file() {
    if [ -f "$1" ]; then
        md5sum "$1" | awk '{print $1}'
    fi
}

clear_laravel_cache_files() {
    mkdir -p bootstrap/cache
    rm -f bootstrap/cache/*.php bootstrap/cache/*.tmp || true
}

run_composer_install() {
    clear_laravel_cache_files
    composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts

    if [ -n "$CURRENT_LOCK_HASH" ]; then
        printf '%s' "$CURRENT_LOCK_HASH" > "$LOCK_HASH_FILE"
    fi

    if [ -n "$CURRENT_MANIFEST_HASH" ]; then
        printf '%s' "$CURRENT_MANIFEST_HASH" > "$MANIFEST_HASH_FILE"
    fi
}

if [ ! -f .env ] && [ -f .env.example ]; then
    cp .env.example .env
fi

CURRENT_LOCK_HASH="$(hash_file composer.lock)"
CURRENT_MANIFEST_HASH="$(hash_file composer.json)"

if [ -f "$LOCK_HASH_FILE" ]; then
    STORED_LOCK_HASH="$(cat "$LOCK_HASH_FILE")"
fi

if [ -f "$MANIFEST_HASH_FILE" ]; then
    STORED_MANIFEST_HASH="$(cat "$MANIFEST_HASH_FILE")"
fi

if [ ! -f vendor/autoload.php ]; then
    NEEDS_COMPOSER_INSTALL=1
fi

if [ ! -f vendor/composer/installed.php ] && [ ! -f vendor/composer/installed.json ]; then
    NEEDS_COMPOSER_INSTALL=1
fi

if [ ! -f vendor/composer/autoload_psr4.php ]; then
    NEEDS_COMPOSER_INSTALL=1
fi

if [ -n "$CURRENT_LOCK_HASH" ] && [ "$CURRENT_LOCK_HASH" != "$STORED_LOCK_HASH" ]; then
    NEEDS_COMPOSER_INSTALL=1
fi

if [ -n "$CURRENT_MANIFEST_HASH" ] && [ "$CURRENT_MANIFEST_HASH" != "$STORED_MANIFEST_HASH" ]; then
    NEEDS_COMPOSER_INSTALL=1
fi

if [ "$NEEDS_COMPOSER_INSTALL" -eq 1 ]; then
    run_composer_install
fi

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache || true
chmod -R ug+rwx storage bootstrap/cache || true

clear_laravel_cache_files

if [ -f .env ] && grep -Eq "^APP_KEY=[[:space:]]*$" .env; then
    php artisan key:generate --force || true
fi

if ! php artisan package:discover --ansi; then
    run_composer_install
    clear_laravel_cache_files
    php artisan package:discover --ansi
fi

exec "$@"
