#!/bin/bash

cd /var/www/html

# Generar clave si no existe
php artisan key:generate --force

# Limpiar y optimizar caché
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ejecutar migraciones
php artisan migrate --force

# Ejecutar seeders de SafeRoad
php artisan db:seed --class=SafeRoadRolesSeeder --force
php artisan db:seed --class=SafeRoadUsersSeeder --force

# Crear enlace simbólico de storage
php artisan storage:link

# Iniciar PHP-FPM en segundo plano
php-fpm -D

# Iniciar Nginx en primer plano
nginx -g "daemon off;"