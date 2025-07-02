# 📤 Инструкция по SFTP загрузке файлов

## 🔧 Настройка SFTP в VS Code

### 1. Установка расширения
Установите расширение **SFTP** в VS Code:
- Откройте VS Code
- Перейдите в Extensions (Ctrl+Shift+X)
- Найдите "SFTP" от Natizyskunk
- Установите расширение

### 2. Конфигурация SFTP

У вас уже настроены два профиля SFTP:

#### Профиль 1: Скрипты настройки
- **Файл**: `.vscode/sftp.json`
- **Назначение**: Загрузка скриптов настройки сервера
- **Путь**: `/root/server-setup`

#### Профиль 2: Laravel приложение
- **Файл**: `.vscode/sftp-laravel.json`
- **Назначение**: Загрузка Laravel кода
- **Путь**: `/var/www/b2bstorage.ru`

## 🚀 Использование SFTP

### Загрузка скриптов настройки

1. **Подключение к серверу**:
   ```bash
   ssh root@5.35.85.110
   ```

2. **Подготовка директорий**:
   ```bash
   # Скопируйте prepare-server-dirs.sh на сервер
   chmod +x prepare-server-dirs.sh
   ./prepare-server-dirs.sh
   ```

3. **Загрузка через VS Code**:
   - Откройте папку с скриптами в VS Code
   - Нажмите `Ctrl+Shift+P`
   - Выберите `SFTP: Upload`
   - Выберите файлы для загрузки

### Загрузка Laravel приложения

1. **После настройки сервера**:
   ```bash
   # На сервере
   cd /var/www/b2bstorage.ru
   ```

2. **Загрузка кода**:
   - Используйте профиль `sftp-laravel.json`
   - Загрузите ваш Laravel проект

## 📁 Структура загрузки

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
├── bootstrap/
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── storage/
├── tests/
├── vendor/
├── .env
├── composer.json
└── package.json
```

## 🔑 Команды SFTP в VS Code

### Основные команды
- `Ctrl+Shift+P` → `SFTP: Upload` - загрузить файл
- `Ctrl+Shift+P` → `SFTP: Upload Active File` - загрузить активный файл
- `Ctrl+Shift+P` → `SFTP: Upload Project` - загрузить весь проект
- `Ctrl+Shift+P` → `SFTP: Download` - скачать файл
- `Ctrl+Shift+P` → `SFTP: Sync Local -> Remote` - синхронизация
- `Ctrl+Shift+P` → `SFTP: Sync Remote -> Local` - синхронизация с сервера

### Горячие клавиши
- `Ctrl+Alt+U` - загрузить активный файл
- `Ctrl+Alt+D` - скачать активный файл
- `Ctrl+Alt+S` - синхронизация

## ⚙️ Настройки конфигурации

### Основные параметры
```json
{
    "name": "B2B Storage Server",
    "host": "5.35.85.110",
    "protocol": "sftp",
    "port": 22,
    "username": "root",
    "password": "Mccain_128",
    "remotePath": "/root/server-setup",
    "uploadOnSave": true,
    "useTempFile": false,
    "openSsh": false
}
```

### Параметры безопасности
- `"uploadOnSave": true` - автоматическая загрузка при сохранении
- `"useTempFile": false` - прямая загрузка без временных файлов
- `"ignore"` - игнорируемые файлы и папки

## 🔒 Безопасность

### Рекомендации
1. **Измените пароль** после первой настройки
2. **Используйте SSH ключи** вместо пароля
3. **Ограничьте доступ** только необходимыми директориями
4. **Регулярно обновляйте** пароли

### Настройка SSH ключей
```bash
# Генерация ключа
ssh-keygen -t rsa -b 4096 -C "your_email@example.com"

# Копирование на сервер
ssh-copy-id root@5.35.85.110

# Обновление конфигурации SFTP
# Замените "password" на "privateKeyPath"
```

## 🛠️ Устранение неполадок

### Проблемы подключения
```bash
# Проверка подключения SSH
ssh root@5.35.85.110

# Проверка порта
telnet 5.35.85.110 22

# Проверка прав доступа
ls -la /root/server-setup
ls -la /var/www/b2bstorage.ru
```

### Проблемы с правами
```bash
# Исправление прав доступа
chmod 755 /root/server-setup
chown -R b2buser:www-data /var/www/b2bstorage.ru
chmod -R 755 /var/www/b2bstorage.ru
```

### Проблемы с загрузкой
1. Проверьте размер файлов
2. Убедитесь в достаточном месте на диске
3. Проверьте права доступа к директориям
4. Убедитесь в стабильности интернет-соединения

## 📋 Чек-лист загрузки

### ✅ Подготовка
- [ ] Установлено расширение SFTP в VS Code
- [ ] Настроены конфигурации SFTP
- [ ] Подготовлены директории на сервере
- [ ] Проверено подключение SSH

### ✅ Загрузка скриптов
- [ ] Загружены все .sh файлы
- [ ] Загружены README.md и QUICK_START.md
- [ ] Проверены права доступа к файлам
- [ ] Запущен setup-server.sh

### ✅ Загрузка Laravel
- [ ] Загружен код Laravel приложения
- [ ] Настроен .env файл
- [ ] Установлены зависимости (composer install)
- [ ] Выполнены миграции
- [ ] Проверена работа приложения

## 🎯 Быстрые команды

### На сервере
```bash
# Подготовка директорий
mkdir -p /root/server-setup /var/www/b2bstorage.ru /var/log/b2bstorage

# Проверка загруженных файлов
ls -la /root/server-setup/
ls -la /var/www/b2bstorage.ru/

# Запуск настройки
cd /root/server-setup
chmod +x *.sh
./setup-server.sh
```

### В VS Code
```bash
# Загрузка всех файлов
Ctrl+Shift+P → SFTP: Upload Project

# Синхронизация
Ctrl+Shift+P → SFTP: Sync Local -> Remote

# Просмотр удаленных файлов
Ctrl+Shift+P → SFTP: List
``` 