-- Исправление внешнего ключа для product_transfer_positions
-- Удаляем старый внешний ключ
ALTER TABLE product_transfer_positions 
DROP CONSTRAINT IF EXISTS product_transfer_positions_product_id_fkey;

-- Добавляем новый внешний ключ на products_sklad
ALTER TABLE product_transfer_positions 
ADD CONSTRAINT fk_product_transfer_positions_product_id 
FOREIGN KEY (product_id) REFERENCES products_sklad (id) ON DELETE CASCADE;

-- Проверяем, что ограничение создано
SELECT 
    tc.constraint_name, 
    tc.table_name, 
    kcu.column_name, 
    ccu.table_name AS foreign_table_name,
    ccu.column_name AS foreign_column_name 
FROM 
    information_schema.table_constraints AS tc 
    JOIN information_schema.key_column_usage AS kcu
      ON tc.constraint_name = kcu.constraint_name
      AND tc.table_schema = kcu.table_schema
    JOIN information_schema.constraint_column_usage AS ccu
      ON ccu.constraint_name = tc.constraint_name
      AND ccu.table_schema = tc.table_schema
WHERE tc.constraint_type = 'FOREIGN KEY' 
    AND tc.table_name='product_transfer_positions'
    AND kcu.column_name = 'product_id'; 