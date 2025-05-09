#!/bin/bash

echo "🔄 Iniciando limpeza completa de cache e reset do banco de dados..."

# Limpando todos os caches do Laravel
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan config:cache
./vendor/bin/sail artisan route:clear
./vendor/bin/sail artisan view:clear
./vendor/bin/sail artisan event:clear
./vendor/bin/sail artisan optimize:clear

# Limpando cache do Filament
./vendor/bin/sail artisan filament:clear
./vendor/bin/sail artisan filament:optimize-clear

# Recriando banco de dados com seed
./vendor/bin/sail artisan migrate:fresh --seed

# Reotimizando Laravel e Filament
./vendor/bin/sail artisan config:cache
./vendor/bin/sail artisan route:cache
./vendor/bin/sail artisan view:cache
./vendor/bin/sail artisan event:cache
./vendor/bin/sail artisan icons:cache
./vendor/bin/sail artisan optimize

./vendor/bin/sail artisan filament:optimize

echo "✅ Sistema limpo, banco de dados recriado, seed executado e otimizações refeitas com sucesso."
