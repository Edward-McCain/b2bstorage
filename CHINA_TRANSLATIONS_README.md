# Обновление китайских переводов категорий и подкатегорий

## Описание

Набор скриптов для обновления китайских переводов (`name_china`) в таблицах `categories` и `subcategories` PostgreSQL базы данных из JSON файла.

## Файлы

### 1. `check_db_structure.php`
Проверяет структуру базы данных и наличие поля `name_china`.

### 2. `add_china_columns.sql`
SQL скрипт для добавления поля `name_china` в таблицы (если не существует).

### 3. `update_china_translations.php`
Основной скрипт для обновления китайских переводов из JSON файла.

## Параметры подключения к базе данных

```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=b2bs_local
DB_USERNAME=b2bs_user
DB_PASSWORD=b2bs_password
```

## Пошаговая инструкция

### Шаг 1: Проверка структуры базы данных

```bash
php check_db_structure.php
```

Этот скрипт покажет:
- ✅ Структуру таблиц `categories` и `subcategories`
- ✅ Наличие поля `name_china`
- ✅ Количество записей в таблицах
- ✅ Примеры данных

### Шаг 2: Добавление поля name_china (если нужно)

Если поле `name_china` не существует, выполните SQL скрипт:

```bash
psql -h 127.0.0.1 -p 5432 -d b2bs_local -U b2bs_user -f add_china_columns.sql
```

Или подключитесь к базе и выполните SQL команды вручную.

### Шаг 3: Обновление переводов

```bash
php update_china_translations.php
```

## Что делает основной скрипт

### Источник данных
Использует файл `b2b-project/frontend/cats_chinese_translated.json` с китайскими переводами.

### Логика работы
1. 🔗 **Подключается** к PostgreSQL базе данных
2. 📖 **Читает** JSON файл с переводами
3. 🔄 **Обрабатывает** каждую категорию и подкатегорию:
   - Обновляет `name_china` в таблице `categories` (если поле заполнено в JSON)
   - Обновляет `name_china` в таблице `subcategories` (если поле заполнено в JSON)
4. 📊 **Выводит статистику** обновлений

### Безопасность
- ✅ Использует **подготовленные запросы** (prepared statements)
- ✅ Работает в **транзакции** (откат при ошибке)
- ✅ **Подробное логирование** всех операций
- ✅ **Обработка ошибок** на каждом этапе

## Пример вывода

```
=== Скрипт обновления китайских переводов категорий ===
✅ Подключение к базе данных успешно
✅ JSON файл загружен, найдено 142 категорий

=== Обработка категорий и подкатегорий ===
✅ Подкатегория 'avtobezopasnost-i-zashchita': обновлен китайский перевод
✅ Подкатегория 'avtomobilnye-aksessuary': обновлен китайский перевод
...

✅ Транзакция успешно завершена

=== СТАТИСТИКА ОБНОВЛЕНИЯ ===
📊 Категории:
   - Обновлено: 0
   - Не найдено в БД: 0
   - Ошибок: 0

📊 Подкатегории:
   - Обновлено: 1247
   - Не найдено в БД: 15
   - Ошибок: 0

🎉 Скрипт завершен успешно!
```

## Структура JSON файла

```json
[
    {
        "category_id": "avtomobili-i-aksessuary",
        "name": "Автомобили и аксессуары",
        "name_ru": "Автомобили и аксессуары",
        "name_en": "Cars and Accessories",
        "name_uz": "Avtomobillar va aksessuarlar",
        "subcategories": [
            {
                "subcategory_id": "avtobezopasnost-i-zashchita",
                "name": "Автобезопасность и защита",
                "name_ru": "Автобезопасность и защита",
                "name_en": "Automotive Safety and Security",
                "name_uz": "Avtomobil xavfsizligi va xavfsizligi",
                "name_china": "汽车安全和保护"
            }
        ]
    }
]
```

## Требования

- **PHP 7.4+** с расширением `pdo_pgsql`
- **PostgreSQL** база данных
- **Доступ** к файлу `b2b-project/frontend/cats_chinese_translated.json`

## Устранение проблем

### Ошибка подключения к базе данных
- Проверьте параметры подключения
- Убедитесь, что PostgreSQL запущен
- Проверьте права доступа пользователя

### Файл JSON не найден
- Убедитесь, что файл `b2b-project/frontend/cats_chinese_translated.json` существует
- Проверьте относительный путь к файлу

### Поле name_china не существует
- Выполните SQL скрипт `add_china_columns.sql`
- Или добавьте поле вручную: `ALTER TABLE categories ADD COLUMN name_china VARCHAR(255);` 