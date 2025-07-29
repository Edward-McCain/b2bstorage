-- Проверка и исправление внешних ключей для таблицы products_sklad

-- 1. Проверяем существующие внешние ключи
SELECT 
    tc.table_name, 
    kcu.column_name, 
    ccu.table_name AS foreign_table_name,
    ccu.column_name AS foreign_column_name,
    rc.delete_rule,
    rc.update_rule
FROM 
    information_schema.table_constraints AS tc 
    JOIN information_schema.key_column_usage AS kcu
      ON tc.constraint_name = kcu.constraint_name
      AND tc.table_schema = kcu.table_schema
    JOIN information_schema.constraint_column_usage AS ccu
      ON ccu.constraint_name = tc.constraint_name
      AND ccu.table_schema = tc.table_schema
WHERE tc.constraint_type = 'FOREIGN KEY' 
    AND tc.table_name IN ('write_off_positions', 'receipt_positions', 'inventory_items', 'product_transfer_positions', 'product_operations', 'product_balances', 'product_images');

-- 2. Проверяем, есть ли записи, которые могут помешать удалению
SELECT 'write_off_positions' as table_name, COUNT(*) as count 
FROM write_off_positions 
WHERE product_id = 136
UNION ALL
SELECT 'receipt_positions', COUNT(*) 
FROM receipt_positions 
WHERE product_id = 136
UNION ALL
SELECT 'inventory_items', COUNT(*) 
FROM inventory_items 
WHERE product_id = 136
UNION ALL
SELECT 'product_transfer_positions', COUNT(*) 
FROM product_transfer_positions 
WHERE product_id = 136
UNION ALL
SELECT 'product_operations', COUNT(*) 
FROM product_operations 
WHERE product_id = 136
UNION ALL
SELECT 'product_balances', COUNT(*) 
FROM product_balances 
WHERE product_id = 136
UNION ALL
SELECT 'product_images', COUNT(*) 
FROM product_images 
WHERE product_id = 136;

-- 3. Если нужно, добавляем CASCADE для внешних ключей
-- (выполнять только если внешние ключи не имеют CASCADE)

-- Для write_off_positions
-- ALTER TABLE write_off_positions DROP CONSTRAINT IF EXISTS write_off_positions_product_id_fkey;
-- ALTER TABLE write_off_positions ADD CONSTRAINT write_off_positions_product_id_fkey 
--     FOREIGN KEY (product_id) REFERENCES products_sklad(id) ON DELETE CASCADE;

-- Для receipt_positions
-- ALTER TABLE receipt_positions DROP CONSTRAINT IF EXISTS receipt_positions_product_id_fkey;
-- ALTER TABLE receipt_positions ADD CONSTRAINT receipt_positions_product_id_fkey 
--     FOREIGN KEY (product_id) REFERENCES products_sklad(id) ON DELETE CASCADE;

-- Для inventory_items
-- ALTER TABLE inventory_items DROP CONSTRAINT IF EXISTS inventory_items_product_id_fkey;
-- ALTER TABLE inventory_items ADD CONSTRAINT inventory_items_product_id_fkey 
--     FOREIGN KEY (product_id) REFERENCES products_sklad(id) ON DELETE CASCADE;

-- Для product_transfer_positions
-- ALTER TABLE product_transfer_positions DROP CONSTRAINT IF EXISTS product_transfer_positions_product_id_fkey;
-- ALTER TABLE product_transfer_positions ADD CONSTRAINT product_transfer_positions_product_id_fkey 
--     FOREIGN KEY (product_id) REFERENCES products_sklad(id) ON DELETE CASCADE;

-- Для product_operations
-- ALTER TABLE product_operations DROP CONSTRAINT IF EXISTS product_operations_product_id_fkey;
-- ALTER TABLE product_operations ADD CONSTRAINT product_operations_product_id_fkey 
--     FOREIGN KEY (product_id) REFERENCES products_sklad(id) ON DELETE CASCADE;

-- Для product_balances
-- ALTER TABLE product_balances DROP CONSTRAINT IF EXISTS product_balances_product_id_fkey;
-- ALTER TABLE product_balances ADD CONSTRAINT product_balances_product_id_fkey 
--     FOREIGN KEY (product_id) REFERENCES products_sklad(id) ON DELETE CASCADE;

-- Для product_images
-- ALTER TABLE product_images DROP CONSTRAINT IF EXISTS product_images_product_id_fkey;
-- ALTER TABLE product_images ADD CONSTRAINT product_images_product_id_fkey 
--     FOREIGN KEY (product_id) REFERENCES products_sklad(id) ON DELETE CASCADE; 