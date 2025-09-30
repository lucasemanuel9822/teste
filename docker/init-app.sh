#!/bin/bash

# Script de inicialização da aplicação
# Este script é executado automaticamente quando o container é iniciado

set -e

echo "Iniciando configuração da aplicação..."

# Aguardar o MySQL estar pronto
echo "Aguardando MySQL estar pronto..."
while ! nc -z mysql 3306; do
    echo "MySQL ainda não está pronto, aguardando..."
    sleep 2
done
echo "MySQL está pronto!"

# Aguardar o MongoDB estar pronto
echo "Aguardando MongoDB estar pronto..."
while ! nc -z mongodb 27017; do
    echo "MongoDB ainda não está pronto, aguardando..."
    sleep 2
done
echo "MongoDB está pronto!"

# Aguardar o Redis estar pronto
echo "Aguardando Redis estar pronto..."
while ! nc -z redis 6379; do
    echo "Redis ainda não está pronto, aguardando..."
    sleep 2
done
echo "Redis está pronto!"

# Executar migrations
echo "Executando migrations do banco de dados..."
php artisan migrate --force

# Limpar cache
echo "Limpando cache..."
php artisan cache:clear

# Criar diretórios necessários se não existirem
echo "Criando diretórios necessários..."
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/bootstrap/cache

# Ajustar permissões
echo "Ajustando permissões..."
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

echo "Configuração da aplicação concluída!"
echo "Aplicação pronta para receber requisições!"

# Iniciar Apache
echo "Iniciando Apache..."
exec apache2-foreground
