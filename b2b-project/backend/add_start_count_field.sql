-- Добавление поля start_count (Начальный остаток) в таблицу products_sklad
ALTER TABLE products_sklad ADD COLUMN start_count NUMERIC(15,3) DEFAULT 0;

-- Добавляем комментарий к полю
COMMENT ON COLUMN products_sklad.start_count IS 'Начальный остаток товара';

-- Создаем индекс для оптимизации запросов по начальному остатку
CREATE INDEX idx_products_sklad_start_count ON products_sklad(start_count); 