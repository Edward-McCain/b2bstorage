-- Проверка результатов обновления структуры базы данных

-- 1. Проверка созданных таблиц
SELECT 'product_balances' as table_name, 
       CASE WHEN EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = 'product_balances') 
            THEN 'EXISTS' ELSE 'MISSING' END as status
UNION ALL
SELECT 'product_operations' as table_name, 
       CASE WHEN EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = 'product_operations') 
            THEN 'EXISTS' ELSE 'MISSING' END as status
UNION ALL
SELECT 'product_transfers' as table_name, 
       CASE WHEN EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = 'product_transfers') 
            THEN 'EXISTS' ELSE 'MISSING' END as status
UNION ALL
SELECT 'product_transfer_positions' as table_name, 
       CASE WHEN EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = 'product_transfer_positions') 
            THEN 'EXISTS' ELSE 'MISSING' END as status;

-- 2. Проверка внешних ключей
SELECT 
    tc.table_name, 
    kcu.column_name, 
    ccu.table_name AS foreign_table_name,
    ccu.column_name AS foreign_column_name,
    'EXISTS' as status
FROM information_schema.table_constraints AS tc 
JOIN information_schema.key_column_usage AS kcu
    ON tc.constraint_name = kcu.constraint_name
    AND tc.table_schema = kcu.table_schema
JOIN information_schema.constraint_column_usage AS ccu
    ON ccu.constraint_name = tc.constraint_name
    AND ccu.table_schema = tc.table_schema
WHERE tc.constraint_type = 'FOREIGN KEY' 
    AND tc.table_schema = 'public'
    AND tc.table_name IN ('receipts', 'write_offs', 'inventories', 'product_balances', 'product_operations', 'product_transfers', 'product_transfer_positions')
ORDER BY tc.table_name, kcu.column_name;

-- 3. Проверка индексов
SELECT 
    schemaname,
    tablename,
    indexname,
    'EXISTS' as status
FROM pg_indexes 
WHERE schemaname = 'public'
    AND tablename IN ('receipts', 'write_offs', 'inventories', 'product_balances', 'product_operations', 'products_sklad')
ORDER BY tablename, indexname;

-- 4. Проверка уникальных ограничений
SELECT 
    tc.table_name, 
    tc.constraint_name, 
    kcu.column_name,
    'EXISTS' as status
FROM information_schema.table_constraints tc
JOIN information_schema.key_column_usage kcu 
    ON tc.constraint_name = kcu.constraint_name
WHERE tc.constraint_type = 'UNIQUE' 
    AND tc.table_schema = 'public'
    AND tc.table_name IN ('receipts', 'write_offs', 'inventories', 'product_balances')
ORDER BY tc.table_name, kcu.column_name;

-- 5. Проверка представления
SELECT 'product_balances_view' as view_name,
       CASE WHEN EXISTS (SELECT 1 FROM information_schema.views WHERE table_name = 'product_balances_view') 
            THEN 'EXISTS' ELSE 'MISSING' END as status;

-- 6. Статистика по таблицам
SELECT 
    'product_balances' as table_name,
    COUNT(*) as record_count
FROM product_balances
UNION ALL
SELECT 
    'product_operations' as table_name,
    COUNT(*) as record_count
FROM product_operations
UNION ALL
SELECT 
    'product_transfers' as table_name,
    COUNT(*) as record_count
FROM product_transfers
UNION ALL
SELECT 
    'product_transfer_positions' as table_name,
    COUNT(*) as record_count
FROM product_transfer_positions;

-- 7. Проверка целостности данных
SELECT 
    'receipts with invalid warehouse' as check_name,
    COUNT(*) as invalid_records
FROM receipts r
LEFT JOIN warehouses w ON r.warehouse = w.id
WHERE w.id IS NULL AND r.warehouse IS NOT NULL
UNION ALL
SELECT 
    'write_offs with invalid warehouse' as check_name,
    COUNT(*) as invalid_records
FROM write_offs w
LEFT JOIN warehouses wh ON w.warehouse = wh.id
WHERE wh.id IS NULL AND w.warehouse IS NOT NULL
UNION ALL
SELECT 
    'inventories with invalid warehouse_id' as check_name,
    COUNT(*) as invalid_records
FROM inventories i
LEFT JOIN warehouses w ON i.warehouse_id = w.id
WHERE w.id IS NULL AND i.warehouse_id IS NOT NULL
UNION ALL
SELECT 
    'receipts with invalid user_id' as check_name,
    COUNT(*) as invalid_records
FROM receipts r
LEFT JOIN users u ON r.user_id = u.id
WHERE u.id IS NULL AND r.user_id IS NOT NULL
UNION ALL
SELECT 
    'write_offs with invalid user_id' as check_name,
    COUNT(*) as invalid_records
FROM write_offs w
LEFT JOIN users u ON w.user_id = u.id
WHERE u.id IS NULL AND w.user_id IS NOT NULL
UNION ALL
SELECT 
    'inventories with invalid created_by' as check_name,
    COUNT(*) as invalid_records
FROM inventories i
LEFT JOIN users u ON i.created_by = u.id
WHERE u.id IS NULL AND i.created_by IS NOT NULL; 