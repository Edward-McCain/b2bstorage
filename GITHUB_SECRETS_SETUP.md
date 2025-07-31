# Настройка GitHub Secrets для двух серверов

## Текущие Secrets (Тестовый сервер)
Эти secrets уже настроены в вашем репозитории:

- `SERVER_HOST` - IP тестового сервера
- `SERVER_USER` - пользователь тестового сервера (обычно root)
- `SERVER_SSH_KEY` - приватный SSH ключ для тестового сервера

## Новые Secrets для продакшн сервера

Добавьте следующие secrets в настройках репозитория (Settings → Secrets and variables → Actions):

### 1. PROD_SERVER_HOST
```
45.92.173.142
```

### 2. PROD_SERVER_USER
```
root
```

### 3. PROD_SERVER_SSH_KEY
```
-----BEGIN OPENSSH PRIVATE KEY-----
[Приватный ключ, который будет сгенерирован на сервере]
-----END OPENSSH PRIVATE KEY-----
```

## Обновление существующих Secrets

Переименуйте существующие secrets:

### 1. TEST_SERVER_HOST
Переименуйте `SERVER_HOST` → `TEST_SERVER_HOST`

### 2. TEST_SERVER_USER  
Переименуйте `SERVER_USER` → `TEST_SERVER_USER`

### 3. TEST_SERVER_SSH_KEY
Переименуйте `SERVER_SSH_KEY` → `TEST_SERVER_SSH_KEY`

## Как получить SSH ключ для продакшн сервера

1. Подключитесь к продакшн серверу:
```bash
ssh root@45.92.173.142
```

2. Запустите скрипт настройки:
```bash
chmod +x setup-production-server.sh
./setup-production-server.sh
```

3. Скопируйте приватный ключ, который будет выведен в конце скрипта

4. Добавьте этот ключ в GitHub Secrets как `PROD_SERVER_SSH_KEY`

## Логика деплоя

- **Ветка `main`** → деплой на тестовый сервер
- **Ветка `production`** → деплой на продакшн сервер

## Команды для работы с ветками

```bash
# Создание ветки production
git checkout -b production

# Деплой на тестовый сервер
git add .
git commit -m "Update for testing"
git push origin main

# Деплой на продакшн сервер
git add .
git commit -m "Update for production"
git push origin production
``` 