-- Добавление колонки name_china в таблицу categories
ALTER TABLE categories ADD COLUMN name_china VARCHAR(255);

-- Добавление колонки name_china в таблицу subcategories  
ALTER TABLE subcategories ADD COLUMN name_china VARCHAR(255);

-- Обновление существующих записей (опционально, если нужно заполнить данными)
-- UPDATE categories SET name_china = name WHERE name_china IS NULL;
-- UPDATE subcategories SET name_china = name WHERE name_china IS NULL; 