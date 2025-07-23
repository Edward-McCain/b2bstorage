-- Изменение типа поля start_count с NUMERIC(15,3) на INTEGER в таблице products_sklad

-- 1. Сначала проверим текущие данные (опционально)
-- SELECT start_count, COUNT(*) FROM products_sklad GROUP BY start_count ORDER BY start_count;

-- 2. Изменяем тип поля с автоматическим преобразованием (обрезает дробную часть)
ALTER TABLE products_sklad 
ALTER COLUMN start_count TYPE INTEGER 
USING start_count::INTEGER;

-- 3. Обновляем комментарий к полю
COMMENT ON COLUMN products_sklad.start_count IS 'Начальный остаток товара (целое число)';

-- 4. Проверяем результат
SELECT column_name, data_type, is_nullable, column_default 
FROM information_schema.columns 
WHERE table_name = 'products_sklad' AND column_name = 'start_count';

-- 5. Опционально: пересоздаем индекс если он был
-- DROP INDEX IF EXISTS idx_products_sklad_start_count;
-- CREATE INDEX idx_products_sklad_start_count ON products_sklad(start_count); 