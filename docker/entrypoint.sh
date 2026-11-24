#!/bin/bash
set -e

# Asegurar permisos en directorios requeridos por Laravel
mkdir -p /var/www/html/storage/framework/cache/data
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Si no existe .env, copiar .env.docker o .env.example
if [ ! -f /var/www/html/.env ]; then
    if [ -f /var/www/html/.env.docker ]; then
        cp /var/www/html/.env.docker /var/www/html/.env
    else
        cp /var/www/html/.env.example /var/www/html/.env
    fi
    php artisan key:generate --force || true
fi

exec "$@"
