#!/bin/bash

# Vue.js Frontend Deployment Script
echo "🚀 Starting Vue.js frontend deployment..."

# Build the application
echo "📦 Building Vue.js application..."
npm run build

if [ $? -ne 0 ]; then
    echo "❌ Build failed!"
    exit 1
fi

echo "✅ Build completed successfully!"

# Create deployment directory on server
echo "📁 Creating deployment directory on server..."
ssh root@5.35.85.110 "mkdir -p /var/www/b2bstorage-frontend"

# Upload built files to server
echo "📤 Uploading files to server..."
scp -r dist/* root@5.35.85.110:/var/www/b2bstorage-frontend/

# Set permissions
echo "🔐 Setting permissions..."
ssh root@5.35.85.110 "chown -R www-data:www-data /var/www/b2bstorage-frontend && chmod -R 755 /var/www/b2bstorage-frontend"

echo "✅ Vue.js frontend deployed successfully!"
echo "🌐 Frontend available at: https://b2bstorage.ru" 