#!/bin/bash

# Att-Ch8 Deployment Deployment Script
# Place this file in /var/www/app and run with `sudo ./deploy.sh`

echo "Starting Deployment..."

# Change to application directory
cd /var/www/app || exit

echo "[->] Pulling latest code from master branch..."
git pull origin master

echo "[->] Rebuilding and starting Docker containers..."
docker compose up -d --build

echo "[->] Running database migrations..."
docker exec app-app-1 php artisan migrate --force

echo "[->] Compiling Frontend Assets..."
docker exec app-app-1 npm install
docker exec app-app-1 npm run build

echo "[->] Clearing and caching Laravel optimizations..."
docker exec app-app-1 php artisan optimize:clear
docker exec app-app-1 php artisan config:cache
docker exec app-app-1 php artisan route:cache
docker exec app-app-1 php artisan view:cache

echo "[->] Restarting Queue Workers..."
docker restart app-queue-1

echo "Deployment Complete! 🚀"
