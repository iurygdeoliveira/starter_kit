#!/bin/bash

echo "🔄 Iniciando limpeza completa de cache e reset do banco de dados..."

# Limpando todos os caches do Laravel
php artisan cache:clear
php artisan config:clear
php artisan config:cache
php artisan route:clear
php artisan view:clear
php artisan event:clear
php artisan optimize:clear

# Limpando cache do Filament
php artisan filament:clear
php artisan filament:optimize-clear

# Recriando banco de dados com seed
php artisan migrate:fresh --seed

# Reotimizando Laravel e Filament
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan icons:cache
php artisan optimize

php artisan filament:optimize

echo "✅ Sistema limpo, banco de dados recriado, seed executado e otimizações refeitas com sucesso."
