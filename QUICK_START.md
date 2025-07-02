# 🚀 Быстрый старт - Настройка сервера B2B Storage

## 📋 Что нужно сделать

### 1. Подготовка DNS (в панели Beget)
```
A     b2bstorage.ru     -> 5.35.85.110
A     www.b2bstorage.ru -> 5.35.85.110  
A     ws.b2bstorage.ru  -> 5.35.85.110
```

### 2. Подключение к серверу
```bash
ssh root@5.35.85.110
```

### 3. Загрузка скриптов
```bash
# Создайте директорию
mkdir -p /root/server-setup
cd /root/server-setup

# Скопируйте все файлы .sh и README.md в эту директорию
```

### 4. Запуск автоматической настройки
```bash
chmod +x setup-server.sh
./setup-server.sh
```

## ⚡ Альтернативный способ (по этапам)

Если хотите контролировать каждый этап:

```bash
# 1. Базовая настройка
./server-setup.sh

# 2. Nginx
./nginx-config.sh

# 3. PostgreSQL  
./postgresql-setup.sh

# 4. Redis
./redis-setup.sh

# 5. SSL (после настройки DNS)
./ssl-setup.sh

# 6. Laravel
./laravel-deploy.sh

# 7. Мониторинг
./monitoring-setup.sh

# 8. Проверка здоровья
./health-check.sh
```

## 🔑 Важные пароли
- **PostgreSQL**: `B2B_Storage_2024!`
- **Redis**: `B2B_Redis_2024!`

## 🌐 Результат
- **Сайт**: https://b2bstorage.ru
- **WebSockets**: https://ws.b2bstorage.ru
- **Приложение**: `/var/www/b2bstorage.ru`

## 🛠️ Полезные команды
```bash
# Проверка здоровья
./health-check.sh

# Мониторинг
/usr/local/bin/server-monitor.sh

# Логи Laravel
tail -f /var/www/b2bstorage.ru/storage/logs/laravel.log

# Статус сервисов
systemctl status nginx postgresql redis-server laravel-websockets laravel-queue
``` 