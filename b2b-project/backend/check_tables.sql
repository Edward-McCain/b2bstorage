-- Проверка структуры таблицы product_balances
SELECT column_name, data_type, is_nullable 
FROM information_schema.columns 
WHERE table_name = 'product_balances' 
ORDER BY ordinal_position;

-- Проверка структуры таблицы product_operations
SELECT column_name, data_type, is_nullable 
FROM information_schema.columns 
WHERE table_name = 'product_operations' 
ORDER BY ordinal_position;

-- Проверка существующих индексов
SELECT indexname, indexdef 
FROM pg_indexes 
WHERE tablename IN ('product_balances', 'product_operations'); 