#!/bin/bash

echo "🔄 Iniciando limpeza completa de cache dos componentes..."

# Limpando todos os caches do Laravel
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan config:cache
./vendor/bin/sail artisan route:clear
./vendor/bin/sail artisan view:clear
./vendor/bin/sail artisan event:clear
./vendor/bin/sail artisan optimize:clear
./vendor/bin/sail artisan filament:optimize-clear

# Rebuild Tailwind CSS
echo "🔄 Iniciando reconstrução do CSS do Tailwind..."

yes | npx tailwindcss@3 --input ./resources/css/filament/admin/theme.css --output ./public/css/filament/admin/theme.css --config ./resources/css/filament/admin/tailwind.config.js --minify

# Reotimizando Laravel e Filament
./vendor/bin/sail artisan optimize
./vendor/bin/sail artisan filament:optimize

echo "✅ Cache do Sistema limpo e otimizações refeitas com sucesso."
