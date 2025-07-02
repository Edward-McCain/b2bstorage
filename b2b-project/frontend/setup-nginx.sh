#!/bin/bash

# Nginx Configuration Setup Script
echo "🔧 Setting up Nginx configurations..."

# Upload main frontend configuration
echo "📤 Uploading main Nginx configuration..."
scp nginx-main.conf root@5.35.85.110:/etc/nginx/sites-available/b2bstorage.ru

# Upload API configuration
echo "📤 Uploading API Nginx configuration..."
scp nginx-api.conf root@5.35.85.110:/etc/nginx/sites-available/b2bstorage-api

# Enable sites
echo "🔗 Enabling Nginx sites..."
ssh root@5.35.85.110 "ln -sf /etc/nginx/sites-available/b2bstorage.ru /etc/nginx/sites-enabled/ && ln -sf /etc/nginx/sites-available/b2bstorage-api /etc/nginx/sites-enabled/"

# Test Nginx configuration
echo "🧪 Testing Nginx configuration..."
ssh root@5.35.85.110 "nginx -t"

if [ $? -eq 0 ]; then
    echo "✅ Nginx configuration is valid!"
    
    # Reload Nginx
    echo "🔄 Reloading Nginx..."
    ssh root@5.35.85.110 "systemctl reload nginx"
    
    echo "✅ Nginx configurations applied successfully!"
    echo "🌐 Frontend: https://b2bstorage.ru"
    echo "🔌 API: https://api.b2bstorage.ru"
else
    echo "❌ Nginx configuration test failed!"
    exit 1
fi 