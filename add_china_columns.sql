-- SQL скрипт для добавления поля name_china в таблицы categories и subcategories

-- Добавляем поле name_china в таблицу categories (если не существует)
DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_name = 'categories' AND column_name = 'name_china'
    ) THEN
        ALTER TABLE categories ADD COLUMN name_china VARCHAR(255);
        RAISE NOTICE 'Поле name_china добавлено в таблицу categories';
    ELSE
        RAISE NOTICE 'Поле name_china уже существует в таблице categories';
    END IF;
END $$;

-- Добавляем поле name_china в таблицу subcategories (если не существует)
DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_name = 'subcategories' AND column_name = 'name_china'
    ) THEN
        ALTER TABLE subcategories ADD COLUMN name_china VARCHAR(255);
        RAISE NOTICE 'Поле name_china добавлено в таблицу subcategories';
    ELSE
        RAISE NOTICE 'Поле name_china уже существует в таблице subcategories';
    END IF;
END $$;

-- Проверяем результат
SELECT 
    'categories' as table_name,
    column_name,
    data_type,
    is_nullable
FROM information_schema.columns 
WHERE table_name = 'categories' AND column_name = 'name_china'

UNION ALL

SELECT 
    'subcategories' as table_name,
    column_name,
    data_type,
    is_nullable
FROM information_schema.columns 
WHERE table_name = 'subcategories' AND column_name = 'name_china'; 