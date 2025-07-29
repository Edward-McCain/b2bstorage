#!/bin/bash
# Быстрое переключение на локальную базу данных
sed -i '' 's/LOCAL_DB=false/LOCAL_DB=true/g' b2b-project/backend/.env && cd b2b-project/backend && php artisan config:clear && echo "✅ Переключено на локальную БД" 