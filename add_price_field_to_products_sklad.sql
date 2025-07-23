-- Добавление поля price (цена товара) в таблицу products_sklad
ALTER TABLE products_sklad 
ADD COLUMN price NUMERIC(15,2) DEFAULT 0 NOT NULL;

-- Добавляем комментарий к полю
COMMENT ON COLUMN products_sklad.price IS 'Цена товара';

-- Создаем индекс для оптимизации запросов по цене (опционально)
CREATE INDEX idx_products_sklad_price ON products_sklad(price);

-- Проверяем, что поле добавлено
SELECT column_name, data_type, is_nullable, column_default 
FROM information_schema.columns 
WHERE table_name = 'products_sklad' AND column_name = 'price'; 