#!/bin/bash
# Быстрое переключение на серверную базу данных
sed -i '' 's/LOCAL_DB=true/LOCAL_DB=false/g' b2b-project/backend/.env && cd b2b-project/backend && php artisan config:clear && echo "✅ Переключено на серверную БД" 