#!/bin/bash

echo "🚀 Starting deployment..."

# Pull latest code
echo "📥 Pulling latest code from GitHub..."
git pull origin main

# Update composer dependencies
echo "📦 Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

# Build frontend
echo "🎨 Building frontend..."
cd frontend
npm install
npm run build
cd ..

# Clear cache
echo "🧹 Clearing cache..."
php artisan cache:clear

# Set permissions
echo "🔐 Setting permissions..."
chmod -R 775 storage bootstrap/cache

# Restart PHP-FPM
echo "🔄 Restarting PHP-FPM..."
sudo systemctl restart php8.1-fpm

echo "✅ Deployment completed successfully!"
