# Настройка локальной базы данных для разработки

## Обзор

Этот документ описывает процесс настройки локальной копии базы данных PostgreSQL для разработки, что позволит работать с данными локально без необходимости подключения к удаленному серверу.

## Преимущества локальной разработки

- ⚡ Быстрые запросы к базе данных
- 🔒 Безопасность (данные не передаются по сети)
- 🛠️ Возможность экспериментировать с данными
- 📊 Полный контроль над структурой базы данных

## Шаг 1: Установка PostgreSQL

### macOS
```bash
brew install postgresql
brew services start postgresql
```

### Ubuntu/Debian
```bash
sudo apt-get update
sudo apt-get install postgresql postgresql-contrib
sudo systemctl start postgresql
sudo systemctl enable postgresql
```

## Шаг 2: Настройка локальной базы данных

1. Сделайте скрипты исполняемыми:
```bash
chmod +x setup_local_database.sh
chmod +x create_database_dump.sh
chmod +x import_database_dump.sh
```

2. Настройте локальную базу данных:
```bash
./setup_local_database.sh b2bs_local b2bs_user b2bs_password
```

## Шаг 3: Создание дампа с сервера

1. Создайте дамп базы данных с сервера:
```bash
./create_database_dump.sh
```

При запросе пароля введите: `B2B_Storage_2024!`

## Шаг 4: Импорт дампа в локальную базу

```bash
./import_database_dump.sh b2bs_local b2bs_user database_dump.sql
```

## Шаг 5: Настройка Laravel

1. Скопируйте файл `env_example.txt` в `b2b-project/backend/.env`:
```bash
cp env_example.txt b2b-project/backend/.env
```

2. Сгенерируйте ключ приложения:
```bash
cd b2b-project/backend
php artisan key:generate
```

3. Очистите кэш конфигурации:
```bash
php artisan config:clear
php artisan cache:clear
```

## Шаг 6: Проверка подключения

Запустите тестовый запрос:
```bash
cd b2b-project/backend
php artisan tinker
```

В tinker выполните:
```php
DB::connection()->getPdo();
```

Если подключение успешно, вы увидите объект PDO.

## Переключение между локальной и серверной базой

### Для локальной разработки:
В файле `.env` установите:
```
LOCAL_DB=true
```

### Для работы с серверной базой:
В файле `.env` установите:
```
LOCAL_DB=false
```

## Структура файлов

- `setup_local_database.sh` - настройка локальной PostgreSQL
- `create_database_dump.sh` - создание дампа с сервера
- `import_database_dump.sh` - импорт дампа в локальную базу
- `env_example.txt` - пример конфигурации .env
- `DatabaseServiceProvider.php` - автоматическое переключение БД

## Обновление локальной базы данных

Для обновления локальной базы данных новыми данными с сервера:

1. Создайте новый дамп:
```bash
./create_database_dump.sh
```

2. Удалите старую локальную базу:
```bash
dropdb -h localhost -U b2bs_user b2bs_local
createdb -h localhost -U b2bs_user b2bs_local
```

3. Импортируйте новый дамп:
```bash
./import_database_dump.sh b2bs_local b2bs_user database_dump.sql
```

## Устранение неполадок

### Ошибка подключения к PostgreSQL
```bash
# Проверьте статус PostgreSQL
brew services list | grep postgresql
# или
sudo systemctl status postgresql
```

### Ошибка прав доступа
```bash
# Создайте пользователя PostgreSQL
createuser -s b2bs_user
```

### Ошибка импорта дампа
```bash
# Проверьте размер файла дампа
ls -lh database_dump.sql
# Проверьте права доступа к файлу
chmod 644 database_dump.sql
```

## Безопасность

- Никогда не коммитьте файл `.env` в git
- Храните пароли в безопасном месте
- Регулярно обновляйте локальную базу данных
- Делайте резервные копии перед обновлением

## Производительность

После настройки локальной базы данных вы заметите:
- Значительное ускорение запросов
- Снижение нагрузки на сервер
- Более стабильную работу приложения 