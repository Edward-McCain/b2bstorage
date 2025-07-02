# ⚡ Быстрый старт SFTP

## 🚀 За 5 минут

### 1. Установите расширение SFTP в VS Code
- Откройте VS Code
- `Ctrl+Shift+X` → найдите "SFTP" → установите

### 2. Подключитесь к серверу
```bash
ssh root@5.35.85.110
# Пароль: Mccain_128
```

### 3. Подготовьте директории на сервере
```bash
# Скопируйте этот файл на сервер и запустите:
chmod +x prepare-server-dirs.sh
./prepare-server-dirs.sh
```

### 4. Загрузите файлы через VS Code
- Откройте папку с проектом в VS Code
- `Ctrl+Shift+P` → `SFTP: Upload Project`
- Выберите профиль "B2B Storage Server"

### 5. Запустите настройку
```bash
cd /root/server-setup
chmod +x setup-server.sh
./setup-server.sh
```

## 🔧 Готовые конфигурации

### Для скриптов настройки
```json
// .vscode/sftp.json
{
    "name": "B2B Storage Server",
    "host": "5.35.85.110",
    "username": "root",
    "password": "Mccain_128",
    "remotePath": "/root/server-setup",
    "uploadOnSave": true
}
```

### Для Laravel приложения
```json
// .vscode/sftp-laravel.json
{
    "name": "B2B Storage Laravel",
    "host": "5.35.85.110",
    "username": "root",
    "password": "Mccain_128",
    "remotePath": "/var/www/b2bstorage.ru",
    "uploadOnSave": true
}
```

## 🎯 Быстрые команды

### В VS Code
- `Ctrl+Alt+U` - загрузить файл
- `Ctrl+Alt+D` - скачать файл
- `Ctrl+Alt+S` - синхронизация

### На сервере
```bash
# Проверка загруженных файлов
ls -la /root/server-setup/

# Запуск настройки
cd /root/server-setup && ./setup-server.sh

# Проверка здоровья
./health-check.sh
```

## 📁 Что загружать

### Этап 1: Скрипты настройки
```
/root/server-setup/
├── setup-server.sh
├── server-setup.sh
├── nginx-config.sh
├── postgresql-setup.sh
├── redis-setup.sh
├── ssl-setup.sh
├── laravel-deploy.sh
├── monitoring-setup.sh
├── health-check.sh
├── README.md
└── QUICK_START.md
```

### Этап 2: Laravel приложение
```
/var/www/b2bstorage.ru/
├── app/
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── storage/
└── .env
```

## ✅ Готово!

После загрузки и настройки:
- 🌐 Сайт: https://b2bstorage.ru
- 🔌 WebSockets: https://ws.b2bstorage.ru
- 🗄️ База данных: PostgreSQL
- 🔴 Кэш: Redis
- 📊 Мониторинг: активен 