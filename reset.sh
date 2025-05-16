#!/bin/bash

echo "🔄 Iniciando limpeza completa de cache dos componentes..."

# Verificando e criando diretórios de armazenamento necessários
echo "📁 Verificando diretórios de armazenamento..."

# Lista de diretórios necessários
directories=(
    "storage/framework/views"
    "storage/framework/cache"
    "storage/framework/sessions"
    "storage/logs"
    "bootstrap/cache"
)

# Verifica e cria os diretórios se necessário
for dir in "${directories[@]}"
do
    if [ ! -d "$dir" ]; then
        echo "Criando diretório $dir"
        mkdir -p "$dir"
    else
        echo "Diretório $dir já existe"
    fi
done

# Configurando permissões corretas
echo "🔒 Configurando permissões..."
sudo chmod -R 775 storage
sudo chmod -R 775 bootstrap/cache

# Limpando todos os caches do Laravel
echo "🧹 Limpando caches..."
php artisan optimize:clear
php artisan filament:optimize-clear

# Rebuild Tailwind CSS
echo "🔄 Iniciando reconstrução do CSS do Tailwind..."

yes | npx tailwindcss@3 --input ./resources/css/filament/admin/theme.css --output ./public/css/filament/admin/theme.css --config ./resources/css/filament/admin/tailwind.config.js --minify

echo "✅ Cache do Sistema limpo"